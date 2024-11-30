<?php
   /* ##########################################################################
    *  _______    _     _                 _____ _____
    * |__   __|  | |   | |          /\   |  __ \_   _|
    *    | | __ _| |__ | | ___     /  \  | |__) || |
    *    | |/ _` | '_ \| |/ _ \   / /\ \ |  ___/ | |
    *    | | (_| | |_) | |  __/  / ____ \| |    _| |_
    *    |_|\__,_|_.__/|_|\___| /_/    \_\_|   |_____|
    *    / ____|          | |           | | |
    *   | |     ___  _ __ | |_ _ __ ___ | | | ___ _ __
    *   | |    / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
    *   | |___| (_) | | | | |_| | | (_) | | |  __/ |
    *    \_____\___/|_| |_|\__|_|  \___/|_|_|\___|_|
    *
    * Really cool stuff happens here
    *
    * ########################################################################## */
    //this function obtained from PHPPro blog.
    function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
    function errorOff($message='Encountered Error') {
        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cgi') {
            header("Status: 400 Bad Request");
        } else {
            header("HTTP/1.1 400 Bad Request");
        }        
        header('Content-type: application/json');
        die('{ "error": "'.$message.'" }');
     }
    ob_start();

    chdir('app');
    require_once('Humble.php');
    $status = Environment::getApplication('api',true);
    if (!isset($status['enabled']) || !(int)$status['enabled']) {
        errorOff('API is disabled');
    }
    session_start();
    if (!isset($_SESSION['uid'])) {
        errorOff('Not Logged In, Authenticate First');
    }
    $headers         = getallheaders();
    $request_method  = strtolower($_SERVER['REQUEST_METHOD']);
    $error           = false;
    $results         = false;
    $content         = [];
    if (($request_method === "put") || ($request_method === "post")) {
        $data        = (string)file_get_contents("php://input");
        $content     = json_decode($data,true);
    } else if ( $request_method === 'delete') {
        foreach (explode('&',(string)file_get_contents("php://input")) as $value_pair) {
            $eqpos   = strpos($value_pair,'=');
            if ($var = substr($value_pair,0,$eqpos)) {
                $content[$var] = substr($value_pair,$eqpos+1);
            }
        }
    } else if (($request_method === 'get')) {
        $reserved = ['t'=>true,'m'=>true,'n'=>true];
        foreach ($_GET as $var => $val) {
            if (!isset($reserved[$var])) {
                $content[$var] = $val;
            }
        }
    }

    $illegal         = ['paradigm'=>true,"humble"=>true,"workflow"=>true];
    $table           = isset($_GET['t'])   ? $_GET['t'] : false;
    $action          = (isset($_GET['m'])) ? $_GET['m'] : ((isset($content['id']) && $content['id']) ? $content['id'] : false);
    $namespace       = isset($_GET['n'])   ? $_GET['n'] : false;
    $module          = \Humble::module($namespace);
    if (isset($illegal[$namespace])) {
        errorOff("Core modules are not accessible via the API");
    }
    /*
     * If table api action is an INT or undefined, use the implied CRUD to REST mappings
     *
     * create → POST    /collection
     * read → GET       /collection[/id]
     * update → PUT     /collection/id
     * patch → PATCH    /collection/id
     * delete → DELETE  /collection/id
     *
     */
    if (($action == 'undefined') || (is_numeric($action))){
        // Optional switch for exceptions where table primary key is not 'id'

        //@TODO: REVIEW THIS!
        $table_key_id   = 'id';
        if ($action == 'undefined') {
            if ($content && empty($content[$table_key_id])){
              //posting undefined, with no id, change to add which returns results
                $action = 'add';
            } else {
                $action = $request_method;
            }
        } else {
            // READ, UPDATE, DELETE
            // Store action as the id, we are assuming the action passed in was an ID number
            $id = $action;
            //
            // We can't use put right now b/c it is not returning anything, use add instead
            if ($request_method == 'put'){
                $action  = 'save';
            } else {
                $content = array_merge($content,[$table_key_id => $id]);
            // now set action by the request method
                $action  = $request_method;
            }
        }

    } else if (substr($action,0,5)=='edit/') {
        /*
         * If the person wants to edit a row in this table, we are going to dynamically build them an edit form
         * @TODO: REVIEW THIS!
         */
        $data   = explode('/',$action);
        $id     = isset($data[1]) ? $data[1] : null;
        if ($id) {
            \Log::general('id: '.session_id());
            $module = \Humble::module($namespace);
            if (isset($module['schema_layout']) && ($module['schema_layout'])) {
                try {
                    $editForm = \Humble::model('humble/renderer');
                    $editForm->setNamespace($namespace);
                    $editForm->setPackage($module['package']);
                    $editForm->setId($id);
                    $editForm->setEntity($table);
                    $editForm->setSessionId(true);
                    print($editForm->render());
                    die();
                } catch (Exception $e) {
                    Environment::standard($e,'Table API Error','api');
                }
            }
        }
    }

    if (!$content) {
        $content = array();  //need to pass in some kind of criteria...
    }
    if ($namespace && $table && $action) {
        //if (!empty($content)) {
            if ($module) {
                header('Content-type: application/json');
                $ref = \Humble::entity($namespace.'/'.$table);
                if (isset($_REQUEST['rows']) && isset($_REQUEST['page'])) {
                    $ref->_rows((int)$_REQUEST['rows']);
                    $ref->_page((int)$_REQUEST['page']);
                }

                foreach ($content as $name => $val) {
                    if ($name !== "") {
                        $method = 'set'.underscoreToCamelCase($name, true);
                        $ref->$method($val);
                    }
                }

                switch ($action) {
                    case    "get"       :
                    case    "fetchOne"  :
                    case    "load"      :   $results = $ref->load(true);
                                            break;
                    case    "query"     :   $results = $ref->load(true);
                                            break;
                    case    "listAll"   :
                    case    "fetchAll"  :   $results = $ref->fetch(true);
                                            break;
                    case    "list"      :
                    case    "fetch"     :   $results = $ref->fetch(true);
                                            break;
                    case    "post"      :
                    case    "save"      :
                                            $results = ['id'=>$ref->save()];
                                            break;
                    case    "put"       :
                    case    "update"    :
                                            $ref->update();
                                            break;
                    case    "add"       :   $results = ['id'=>$ref->add()];
                                            break;
                    case    "delete"    :   $results = ['delete'=>$ref->delete()];  //Delete didn't return anything, maybe return a boolean if delete was successful?
                                            break;
                                            break;
                    default             :   try {
                                                $results = $ref->$action();
                                            } catch (Exception $ex) {
                                                $results = print_r($ex,true);
                                            }
                                            break;
                }
            } else {
                errorOff('The module you are trying to access either does not exist or is disabled');
            }
     //   } else {
       //     $error = array('error'=>'No content was passed, unable to process');
      //  }
   } else if ($namespace && $table) {
    
   } else {
       errorOff('Not enough data to use the API');
   }

   if ($results) {
       print(is_array($results) ? json_encode($results) : $results);
   }