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

    //--------------------------------------------------------------------------
    //Basic setup stuff
    require_once('Humble.php');
    require_once('Environment.php');

    //--------------------------------------------------------------------------
    //Variable Initialization
    $drop            = ['humble_api_namespace'=>true,'humble_api_entity'=>true,'humble_api_method'=>true];   //Don't accidentally set these 
    $illegal         = ['paradigm'=>true,"humble"=>true,"workflow"=>true,'admin'=>true,'contrive'=>true];    //These modules are not allowed access to the API
    $headers         = getallheaders();
    $request_method  = strtolower($_SERVER['REQUEST_METHOD']);
    $content_type    = strtolower($_SERVER['CONTENT_TYPE']??'application/x-www-form-urlencoded');
    $error           = false;
    $results         = false;
    $content         = [];
    $namespace       = $_GET['humble_api_namespace'] ?? false;
    $entity_alias    = $_GET['humble_api_entity']    ?? false;
    $action          = $_GET['humble_api_method']    ?? false;
    $project         = Environment::getProject();
    \Humble::_namespace($namespace);
    \Humble::_controller($entity_alias);

    //--------------------------------------------------------------------------
    //Lets check to see if this API is enabled in the application.xml file
    $status          = Environment::getApplication('api',true);
    if (!isset($status['enabled']) || !(int)$status['enabled']) {
        errorOff('API is disabled');
    }
    
    //--------------------------------------------------------------------------
    //Too dangerous to let the API hit framework tables, this prevents that
    if (isset($illegal[$namespace])) {
        errorOff("Framework modules are not accessible via the API");
    }
    
    //--------------------------------------------------------------------------
    //If we get this far, read and process different types of input
    switch ($content_type) {
        case "application/json" : 
            $content = json_decode(str_replace("\n","",file_get_contents("php://input")),true);
            break;
        case "application/x-www-form-urlencode" :
            foreach (explode('&',(string)file_get_contents("php://input")) as $value_pair) {
                $eqpos   = strpos($value_pair,'=');
                if ($var = substr($value_pair,0,$eqpos)) {
                    $content[$var] = substr($value_pair,$eqpos+1);
                }
            }
            break;
        default:
            break;
    }    
    
    //--------------------------------------------------------------------------
    //Ex: /api/mod/alias/3  <- if action numeric, map that to the ID field
    if (is_numeric($action) && (!isset($content['id']))) {
        $content['id'] = $action;
        $action = null;
    } else {
        \Humble::_action($action);
    }

    //--------------------------------------------------------------------------
    //Policy allows for different actions depending on users logged in status
    session_start();
    $user_state      = isset($_SESSION['user']['id']) ? 'authenticated' : 'public';
    
    //--------------------------------------------------------------------------
    //Let's see what the api policy says about what actions can be done on entity
    $policy_file     = 'Code/'.$project->package.'/'.$project->module.'/etc/api_policy.json';
    if (!file_exists($policy_file)) {
        errorOff('No API Policy Found');
    }
    $policy          = json_decode(file_get_contents($policy_file),true);
    
    //--------------------------------------------------------------------------
    //If no data was passed, abort and leave
    if (!$content) {
        errorOff("Request contained no content");
    }

    if ($module          = \Humble::module($namespace)) {
        if (!$entity     = Humble::entity('humble/entities')->setNamespace($namespace)->setAlias($entity_alias)->load(true)) {
            errorOff("Entity not found");
        }
        $defaults       = $policy['default'][$user_state];
        $entity_policy  = isset($policy['entities'][$namespace][$entity['entity']]) ? $policy['entities'][$namespace][$entity['entity']][$user_state] : $defaults;
        $entity         = \Humble::entity($entity['namespace'].'/'.$entity['entity']);
        $rows_var       = (isset($policy['entities'][$namespace][$entity['entity']]['pagination']['rows'])) ? $policy['entities'][$namespace][$entity['entity']]['pagination']['rows'] : 'rows';
        $page_var       = (isset($policy['entities'][$namespace][$entity['entity']]['pagination']['page'])) ? $policy['entities'][$namespace][$entity['entity']]['pagination']['page'] : 'page';
        if (isset($_REQUEST[$rows_var])) {
            $entity->_rows($_REQUEST[$rows_var]);
        }
        if (isset($_REQUEST[$page_var])) {
            $entity->_page($_REQUEST[$page_var]);
        }
        /*
         * SOMEWHERE IN HERE HANDLE THE OPTIONAL PAGINATION
         */
        foreach ($content as $var => $value) {
            $method = 'set'.underscoreToCamelCase($var,true);
            $entity->$method($value);
        }        
        switch ($request_method) {
            case "get"  :
                if (isset($entity_policy['read']) && ($entity_policy['read'])) {
                    $results = json_encode($entity->load());
                } else {
                    errorOff('API Policy Violation');
                }
                break;
            case "post" :
                if (isset($entity_policy['read']) && ($entity_policy['read'])) {
                    $results = $entity->fetch();
                } else {
                    errorOff('API Policy Violation');
                }
                break;
            case "put"  :
                if (isset($entity_policy['write']) && ($entity_policy['write'])) {
                    $results = ['id'=>$entity->save()];
                } else {
                    errorOff('API Policy Violation');
                }
                break;
            case "delete" :
                if (isset($entity_policy['write']) && ($entity_policy['write'])) {
                    $results = ['rows_affected'=>$entity->delete()];
                } else {
                    errorOff('API Policy Violation');
                }
                break;
            default:
                errorOff('Invalid Request Method ['.$request_method.']');
                break;
        }

    } else {
        errorOff('Module missing or is disabled');
    }

    if ($results) {
        print(is_array($results) ? json_encode($results) : $results);
    }
