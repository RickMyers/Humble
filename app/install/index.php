<?php
/*############################################################################################
 ______               _      _____            _             _ _
|  ____|             | |    / ____|          | |           | | |
| |__ _ __ ___  _ __ | |_  | |     ___  _ __ | |_ _ __ ___ | | | ___ _ __
|  __| '__/ _ \| '_ \| __| | |    / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
| |  | | | (_) | | | | |_  | |___| (_) | | | | |_| | | (_) | | |  __/ |
|_|  |_|  \___/|_| |_|\__|  \_____\___/|_| |_|\__|_|  \___/|_|_|\___|_|

This software is licensed under GNU GPL.

For more information, see the file LICENSE.txt

//------------------------------------------------------------------------------
                                      */
function underscoreToCamelCase( $string, $first_char_caps = false) {
    return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
}

ob_start();                  //Must do this to capture all headers before passing to client
chdir('app');                //This is the root directory of the application
require_once('Humble.php');  //This is the engine of the whole system

$workflowRC   = null;        //Workflow global variables...
$cancelBubble = false;       // These indicate whether the workflow completed
//###########################################################################
//In the case where we consume our own services, we need to pass the session
//id in the request so that we aren't re-routed to the login screen
if (isset($_REQUEST['sessionId'])) {
    session_id($_REQUEST['sessionId']);
}
session_start();

//###########################################################################
//This checks to see if the system has been taken down for maintenance, and
//if not, it returns us whether we are running with authorization checks in
//place.  We would only disable authorization checks if the system were in
//an unusable state and we were doing aggressive debugging or testing
$authorizationEngineEnabled = \Environment::statusCheck($_GET['n'],$_GET['c'],$_GET['m']);
$namespace       = $_GET['n'];
$controller      = $_GET['c'];
$method          = $_GET['m'];
$bypass          = false;
$headers         = getallheaders();

//###########################################################################
//Two phased login check.  If you are not logged in (determined by having a
//variable called uid in the session, then load the list of services a person
//can access without being logged in.  If the service you are trying to load
//is on the list, then you are allowed to pass, otherwise you are routed to
//the login screen
if (!isset($_SESSION['uid'])) {
    //check to see if the service they are trying to access is publicly visible
    $URI     = '/'.$namespace.'/'.$controller.'/'.$method;
    $access  = false;
    $allowed = json_decode(file_get_contents('allowed.json'));
    foreach ($allowed->routes as $route) {
        if ($route->URI === $URI) {
            $access = true;
        }
    }
    if ($access) {
        $bypass = true;
        //NOP, you are ok to hit that resource
    } else {
        //Go log in!
        header("Location: /index.html?m=You Must Log In");
    }
}

//###########################################################################
//Allows for custom headers to be created and passed to the client
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Expose-Headers: Errors, Warnings, Notices, Messages, Alerts, Pagination');

//###########################################################################
//Basic variable initilization, with us extracting the values of the URI
//identifying this service
$recompile       = false;           //Do we need to recompile the controller
$info            = array();         //Initialize the controller info array


//###########################################################################
//Primes the Humble Engine
$ns              = $namespace;      //save a copy, since this might change if there's no specific action but there is a default action
\Humble::_namespace($namespace);     //this will become the inherited namespace if necessary
\Humble::_controller($controller);
\Humble::_action($method);
\Environment::isAjax(isset($headers['HTTP_X_REQUESTED_WITH']) && ($headers['HTTP_X_REQUESTED_WITH']==='xmlhttprequest'));

//###########################################################################
//Gets information about the module you are trying to interact with, by using
//the namespace.  If nothing comes back, the module is disabled or does not exist
$module          = \Humble::getModule($namespace);
if (!$module) {
    //@TODO: change this to throw an exception
    die('The module/feature (ns='.$namespace.',cn='.$controller.',mt='.$method.') you are trying to access either does not exist or is disabled');
} else {
    $core            = \Humble::getModule('&&NAMESPACE&&');    //A reference to the core functionality held in the Core module
    $include         = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controller_cache']).'/'.$controller.'Controller.php';
    $source          = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controller']).'/'.$controller.'.xml';

    //###########################################################################
    //We first look for the controller that contains the service in the module
    //identified by the namespace.  If we don't find the controller, we go check
    //if there is a controller of that name in our default core module, which has
    //our standard set of services.  This gives the chance for a client module to
    //override a core service
    if (file_exists($include)) {
        $info        = \Humble::getController($namespace.'/'.$controller);
        $recompile   = ($info['compiled'] != date("F d Y, H:i:s", filemtime($source)));
    } else if (file_exists($source)) {
        $recompile   = true;
    } else {
        //now lets look for it in the default controllers [SET TO THE DEFAULT MODULE]
        $include         = 'Code/&&PACKAGE&&/&&MODULE&&/Controllers/Cache/'.$controller.'Controller.php';
        $source          = 'Code/&&PACKAGE&&/&&MODULE&&/Controllers/'.$controller.'.xml';
        if (file_exists($include)) {
            $ns          = '&&DEFAULT_NAMESPACE&&';   //we mark that we are in fact using the default namespace action without specifically changing the official namespace
            $info        = \Humble::getController($ns.'/'.$controller);
            $recompile   = ($info['compiled'] != date("F d Y, H:i:s", filemtime($source)));
        } else if (file_exists($source)) {
            $ns          = 'core';
            $info        = \Humble::getController($ns.'/'.$controller);
            $recompile   = true;
        } else {
            //@TODO: Can I have an exception please?
            die('FRONT CONTROLLER: Could not find the controller to route this request ('.$namespace.','.$controller.','.$method.')');
        }
    }

    //###########################################################################
    //If we detected a change in the controller XML, find the proper destination
    //directory from the module and compile and place the new controller there
    if ($recompile) {
        try {
            \Log::console('Recompiling: '.$source.' into '.$include);
            $identifier = $ns.'/'.$controller;
            $compiler   = \Environment::getCompiler();
            $compiler->setController($controller);
            if ($ns === '&&NAMESPACE&&') {
                $compiler->setInfo($core);
                $compiler->setSource($core['package'].'/'.str_replace('_','/',$core['controller']));
                $compiler->setDestination($core['package'].'/'.str_replace('_','/',$core['controller_cache']));
            } else {
                $compiler->setInfo($module);
                $compiler->setSource($module['package'].'/'.str_replace('_','/',$module['controller']));
                $compiler->setDestination($module['package'].'/'.str_replace('_','/',$module['controller_cache']));
            }
            $compiler->compile($identifier);
        } catch (ControllerParameterException $e) {
            \HumbleException::standard($e,'Parameter Configuration Error','custom');
        } catch (MissingControllerXMLException $e) {
            \HumbleException::standard($e,'File Name Or Location Error','custom');
        } catch (Exception $e) {
            \HumbleException::standard($e, "Compilation Error");
        }
    }

    //###########################################################################
    //If this call was the result of a redirect, then we rebuild the global
    //variables from the JSON array passed
    if (isset($_GET['redirect'])) {
        if (isset($_SESSION['HUMBLE_REDIRECT_HEADERS'])) {
            foreach ($_SESSION['HUMBLE_REDIRECT_HEADERS'] as $header) {
                header($header);
            }
            unset($_SESSION['HUMBLE_REDIRECT_HEADERS']);
        }
        if (isset($_SESSION[$_GET["POST"]])) {
            $data = json_decode($_SESSION[$_GET["POST"]],true);
            unset($_SESSION[$_GET["POST"]]);
            foreach ($data as $key => $val) {
                $_POST[$key]    = urldecode($val);
                $_REQUEST[$key] = urldecode($val);
            }
        }
    }

    //###########################################################################
    if ($authorizationEngineEnabled && !$bypass) {          //This control is set in the application.xml root file
        require_once "AuthorizationEngine.php";         //Call out to the authorization engine
    }

    //###########################################################################
    if (file_exists('Constants.php')) {
        require_once 'Constants.php';                   //Enumeration type stuff
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {       //This is for handling function that windows PHP is missing
        require_once "Compatibility.php";
    }

    //###########################################################################
    //If we got this far, hand off to the controllers
    try {
        require_once($include);                             //This is where we bring in the controller and the actual action/view processing takes place
    } catch (ValidationRequiredException $e) {
        \HumbleException::standard($e,'Required Parameter Missing','custom');
    } catch (ValidationDatatypeException $e) {
        \HumbleException::standard($e,'Data Validation Error','custom');
    } catch (NoTriggerEventException $e) {
        \HumbleException::standard($e,'Invalid Trigger Event','custom');
    } catch (UnconfiguredException $e) {
        \HumbleException::standard($e,'Workflow Configuration Error','workflow');
    } catch (IncompleteConfigurationException $e) {
        \HumbleException::standard($e,'Workflow Component Error','workflow');
    } catch (SmartyCompilerException $e) {
        \HumbleException::standard($e,'Smarty Compiler Error','smarty');
    } catch (Exception $e) {
        \HumbleException::standard($e,"Error Encountered");
    }
}