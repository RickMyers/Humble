<?php
   /* ##########################################################################
    *  ___  ___          _      _    ___  ______ _____ 
    *  |  \/  |         | |    | |  / _ \ | ___ \_   _|
    *  | .  . | ___   __| | ___| | / /_\ \| |_/ / | |  
    *  | |\/| |/ _ \ / _` |/ _ \ | |  _  ||  __/  | |  
    *  | |  | | (_) | (_| |  __/ | | | | || |    _| |_ 
    *  \_|  |_/\___/ \__,_|\___|_| \_| |_/\_|    \___/ 
    *   _____             _             _ _            
    *  /  __ \           | |           | | |           
    *  | /  \/ ___  _ __ | |_ _ __ ___ | | | ___ _ __  
    *  | |    / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__| 
    *  | \__/\ (_) | | | | |_| | | (_) | | |  __/ |    
    *   \____/\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
    *
    *  Really cool stuff happens here
    *
    * ########################################################################## */
    function underscoreToCamelCase($string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }

    ob_start();

    chdir('app');
    require_once('Humble.php');
    session_start();

    $headers         = getallheaders();
    $request_method  = strtolower($_SERVER['REQUEST_METHOD']);
    $error           = false;
    $results         = false;
    $content         = [];
    if ($request_method === 'put') {
        $data        = (string)file_get_contents('php://input');
        $content     = json_decode($data,true);
    } else if ($request_method === 'post') {
        foreach ($_POST as $var => $val) {
            $content[$var] = $val;
        }
    } else if ($request_method === 'get') {
        $reserved = ['t'=>true,'m'=>true,'n'=>true];
        foreach ($_GET as $var => $val) {
            if (!isset($reserved[$var])) {
                $content[$var] = $val;
            }
        }
    }

    $class           = isset($_GET['t']) ? $_GET['t'] : false;
    $method          = isset($_GET['m']) ? $_GET['m'] : false;
    $namespace       = isset($_GET['n']) ? $_GET['n'] : false;
    if ($module          = \Humble::getModule($namespace)) {
        if ($class && $method && $namespace) {
            if ($model = Humble::getModel($namespace.'/'.$class)) {
                foreach ($content as $name => $val) {
                    if ($name !== '') {
                        $setter = 'set'.underscoreToCamelCase($name, true);
                        $model->$setter($val);
                    }
                }
                $results = $model->$method();
            } else { 
                $error = array('error'=>'Unable to allocate model, wtf?');  
            }
        } else {
            $error = array('error'=>'Namespace, Class, and Method are required [/mapi/namespace/class/method]');  
        }
    } else {
        $error = array('error'=>'The module you are trying to access ['.$namespace.'] either does not exist or is disabled');
    };
    
    if ($error) {
        print(json_encode($error));
    } else if ($results) {
        print(is_array($results) ? json_encode($results,JSON_PRETTY_PRINT) : $results);
    }