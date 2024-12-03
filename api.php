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
    function errorOff($message='Encountered Error') {
        \HumbleException::standard(new Exception($message,12),'API Error','api');
        die();
     }
    ob_start();

    chdir('app');
    require_once('Humble.php');
    require_once('Environment.php');
    $status          = Environment::getApplication('api',true);
    if (!isset($status['enabled']) || !(int)$status['enabled']) {
        errorOff('API is disabled');
    }
    $project         = Environment::getProject();
    $policy_file     = 'Code/'.$project->package.'/'.$project->module.'/etc/api_policy.json';
    if (!file_exists($policy_file)) {
        errorOff('No API Policy File Found');
    }
    $policy          = json_decode(file_get_contents($policy_file,true));
    $drop            = ['humble_api_namespace'=>true,'humble_api_entity'=>true,'humble_api_method'=>true];   //Don't accidentally set these 
 /*   $illegal         = ['paradigm'=>true,"humble"=>true,"workflow"=>true,'admin'=>true,'contrive'=>true];    //These modules are not allowed access to the API
    if (isset($illegal[$namespace])) {
        errorOff("Framework modules are not accessible via the API");
    }*/
    $headers         = getallheaders();
    $request_method  = strtolower($_SERVER['REQUEST_METHOD']);
    $content_type    = strtolower($_SERVER['CONTENT_TYPE']);
    $error           = false;
    $results         = false;
    $content         = [];
    $id              = false;
    \Humble::_namespace($namespace     = $_GET['humble_api_namespace'] ?? false);
    \Humble::_controller($entity_alias = $_GET['humble_api_entity']    ?? false);
    if (is_numeric($action = $_GET['humble_api_method'] ?? false)) {
        $id     = $action;      //probably doing a GET on an id value... action can't be a number
        $action = null;
    } else {
        \Humble::_action($action);
    }
    $data       = '';
    switch ($content_type) {
        case "application/json" : 
            $content = json_decode((string)file_get_contents("php://input"),true);
            break;
        case "application/x-www-form-urlencode" :
            foreach (explode('&',(string)file_get_contents("php://input")) as $value_pair) {
                $eqpos   = strpos($value_pair,'=');
                if ($var = substr($value_pair,0,$eqpos)) {
                    $content[$var] = substr($value_pair,$eqpos+1);
                }
            }
            break;
    }
    
    if (!$content) {
        errorOff("Request contained no content");
    }
    //  : ((isset($content['id']) && $content['id']) ? $content['id'] : false);    
    session_start();
    $user_state      = isset($_SESSION['user_id']) ? 'authenticated' : 'public';
    if ($module          = \Humble::module($namespace)) {
        if (!$entity     = Humble::entity('humble/entities')->setNamespace($namespace)->setAlias($entity_alias)->load(true)) {
            errorOff("Entity not found");
        }
        foreach ($content as $var => $value) {
            $method = 'set'.underscoreToCamelCase($value,true);
            $entity->$method($value);
        }
        print_r($entity);die();    //OK, pick up from here
    };

    
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
