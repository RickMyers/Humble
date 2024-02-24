<?php
/*##############################################################################
 ______               _      _____            _             _ _
|  ____|             | |    / ____|          | |           | | |
| |__ _ __ ___  _ __ | |_  | |     ___  _ __ | |_ _ __ ___ | | | ___ _ __
|  __| '__/ _ \| '_ \| __| | |    / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
| |  | | | (_) | | | | |_  | |___| (_) | | | | |_| | | (_) | | |  __/ |
|_|  |_|  \___/|_| |_|\__|  \_____\___/|_| |_|\__|_|  \___/|_|_|\___|_|

This software is licensed under GNU GPL.

For more information, see the file LICENSE.txt

 ###############################################################################*/
function underscoreToCamelCase($string, $first_char_caps=false) {
    return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
}
//------------------------------------------------------------------------------
function badRequestError() {
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cgi') {
        header("Status: 400 Bad Request");
    } else {
        header("HTTP/1.1 400 Bad Request");
    }
    die();        
}
//------------------------------------------------------------------------------
ob_start();                                                                     //Must do this to capture all headers before passing to client
chdir('app');                                                                   //This is the root directory of the application
require_once('Humble.php');                                                     //This is the engine of the whole system

//###########################################################################
//Scrubba-dub-dub
foreach ($_GET as $var => $val) {
    $_REQUEST[$var] = $_GET[$var] = htmlspecialchars($val,ENT_QUOTES);
}
$namespace              = ($_GET['humble_framework_namespace'] === 'default') ? \Environment::namespace : $_GET['humble_framework_namespace'];
$controller             = $_GET['humble_framework_controller'];
$action                 = $_GET['humble_framework_action'];
$origin                 = 'front_controller';
$method                 = $action;                                              //Because REASONS!!!
$bypass                 = false;                                                //Be wary of setting to true, will make everything public
$home                   = '/index.html';
$login_message          = '?message=Please Log In';
$request_handled        = false;
$headers                = getallheaders();
$useragent              = $_SERVER['HTTP_USER_AGENT'] ?? false;
$mobile_support         = false;
$use_connection_pool    = false;                                                //Set to true in your CUSTOM.php to use persistent SQL connection
$use_redis              = false;                                                //Use redis for cache instead of memcache
$use_pgsql              = false;                                                //Use PostgreSQL instead of MySQL
$is_mobile              = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)));
$is_production          = \Environment::isProduction();
$admin                  = false;

//###########################################################################
//This checks to see if the system has been taken down for maintenance, and
//if not, it returns us whether we are running with authorization checks in
//place.  We would only disable authorization checks if the system were in
//an unusable state and we were doing aggressive debugging or testing
//$authorizationEngineEnabled = \Environment::statusCheck($namespace,$controller,$action);
$authorizationEngineEnabled = true;

//###########################################################################
//If this application is deployed using a Micro-Services Architecture, then
//one of the application nodes must be the router.  If this is the MSA Router,
//include the Micro-Service Router and end.
if (\Environment::MSARouter()) {
    require_once("msa.php");
    die();
}

//###########################################################################
$workflowRC   = null;        //Workflow global variables...
$cancelBubble = false;       // These indicate whether the workflow completed

//###########################################################################
//In the case where we consume our own services, we need to pass the session
//id in the request so that we aren't re-routed to the login screen
if (isset($_REQUEST['sessionId'])) {
    session_id($_REQUEST['sessionId']);
}
session_start();

$admin      = $_SESSION['admin_id'] ?? false;                                   //Are you already logged in as an administrator/super-user?
$logged_in  = $_SESSION['uid']      ?? false;                                   //Are you logged in as a normal user?

//###########################################################################
//Allows for custom code execution at this point if so desired.
//Can also override default flags
if (file_exists('CUSTOM.php')) {
    include 'CUSTOM.php';
}

//###########################################################################
//Are you trying to get to the admin page?
if (!$admin) {
    if (($namespace==='admin') && ($controller==='home') && ($action==='page')) {
        header('Location: /admin/login/form');
        die();
    }
}

//###########################################################################
//Logs you in as a general user if you are an admin and are not logged in
if ($admin && !$logged_in) {
    \Environment::logAdminIn();
}

//###########################################################################
//Two phased login check.  If you are not logged in (determined by having a
//variable called uid in the session, then load the list of services a person
//can access without being logged in.  If the service you are trying to load
//is on the list, then you are allowed to pass, otherwise you are routed to
//the login screen
if (!$bypass && !$logged_in) {
    //check to see if the service they are trying to access is publicly visible
    //We are going to try to get a cached copy, otherwise we read the physical file for routes
//    if (!$allowed = Humble::cache('humble_framework_allowed_routes')) {
//        Humble::cache($allowed = json_decode(file_get_contents('allowed.json')));
//    }
    $allowed = json_decode(file_get_contents('allowed.json'));
    if (isset($allowed->routes->{'/'.$namespace.'/'.$controller.'/'.$action}) || isset($allowed->namespaces->$namespace) || isset($allowed->controllers->{'/'.$namespace.'/'.$controller})) {
        $bypass = true;
        //NOP, you are ok to hit that resource
    } else {
        header("Location: ".$home.$login_message);
        die();
    }
}

//###########################################################################
//Allows for custom headers to be created and passed to the client if the app
// is using a custom headers file
if (file_exists('HEADERS.php')) {
    include 'HEADERS.php';
} else {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
    header('Access-Control-Expose-Headers: Errors, Warnings, Messages, Alerts, Pagination');
}

//###########################################################################
//Basic variable initilization, with us extracting the values of the URI
//identifying this service
$recompile       = false;                                                       //Do we need to recompile the controller
$info            = [];                                                          //Initialize the controller info array

//###########################################################################
//Primes the Humble Engine
$ns              = $namespace;                                                  //save a copy, since this might change if there's no specific action but there is a default action
\Humble::_namespace($namespace);                                                //this will become the inherited namespace if necessary
\Humble::_controller($controller);
\Humble::_action($action);
\Environment::isAjax(isset($headers['HTTP_X_REQUESTED_WITH']) && ($headers['HTTP_X_REQUESTED_WITH']==='xmlhttprequest'));

//###########################################################################
//Gets information about the module you are trying to interact with, by using
//the namespace.  If nothing comes back, the module is disabled or does not exist
if (!($module = \Humble::module($namespace))) {
    \HumbleException::standard(new Exception("Namespace Error, Module Missing Or Disabled",16),"Request Error",'routing');
    badRequestError();
}

//###########################################################################
//If the device/browser is running on a mobile device, check to see if there
// is a custom view/controller for that action for mobile devices
if ($mobile_support && $is_mobile) {
    //perform check for mobile special view
    require_once 'mobile.php';                                                  //include special mobile device logic
}

//###########################################################################
//If the request wasn't handled by the previous step, then drop into the
//  normal handling for a request
if (!$request_handled) {
    $core            = \Humble::module(\Environment::namespace());             //A reference to the core functionality held in the applications primary module
    $include         = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controller_cache']).'/'.$controller.'Controller.php';
    $source          = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controller']).'/'.$controller.'.xml';

    //###########################################################################
    //If App Status is not in a production state, we allow for dynamic compilation of controllers.
    //We first look for the controller that contains the action in the module identified by the namespace used on the URL.
    //If we don't find the controller, we go check in the module that is identified as the base module for the application.
    //If we find a controller in our base module, with an action that matches the URL, we use that.
    if (!$is_production) {
        if (file_exists($include)) {
            $info        = \Humble::controller($namespace.'/'.$controller);
            $recompile   = ($info['compiled'] != date("Y-m-d, H:i:s", filemtime($source)));
        } else if (file_exists($source)) {
            $recompile   = true;                                                //The controller source code exists but it is not currently compiled so flag it for compiling
        } else {
            //No specific controller exists to match request, so let's go look for it in the base application module
            $include         = 'Code/'.$core['package'].'/'.$core['module'].'/Controllers/Cache/'.$controller.'Controller.php';
            $source          = 'Code/'.$core['package'].'/'.$core['module'].'/Controllers/'.$controller.'.xml';
            if (file_exists($include)) {
                $ns          = $core['namespace'];                              //we mark that we are in fact using the default namespace action without specifically changing the official namespace
                $info        = \Humble::controller($ns.'/'.$controller);
                $recompile   = ($info['compiled'] != date("Y-m-d, H:i:s", filemtime($source)));
            } else if (file_exists($source)) {
                $ns          = $core['namespace'];
                $info        = \Humble::controller($ns.'/'.$controller);
                $recompile   = true;
            } else {
                \HumbleException::standard(new Exception("Can Not Route Request, Resource Does Not Exist",12),"Request Error",'routing');
                badRequestError();
            }
        }
    }

    //###########################################################################
    //If we detected a change in the controller XML, find the proper destination
    //directory from the module and compile and place the new controller there
    if ($recompile) {
        try {
            //\Log::console('Recompiling: '.$source.' into '.$include);
            $identifier = $ns.'/'.$controller;
            $compiler   = \Environment::getCompiler();
            $compiler->setController($controller);
            if ($ns === $core['namespace']) {
                $core   = \Humble::module($ns);
                $compiler->setInfo(\Humble::module($core['namespace']));
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
    if ($authorizationEngineEnabled && !$bypass) {          
       require_once "AuthorizationEngine.php";                                  //Call out to the authorization engine
    }

    //###########################################################################
    if (file_exists('Constants.php')) {
        require_once 'Constants.php';                                           //Enumeration type stuff
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {       
        require_once "Compatibility.php";                                       //This is for handling function that windows PHP is missing
    }

    if (!file_exists($include)) {
        \HumbleException::standard(new Exception("Controller Does Not Exist, Check Name [".Environment::state()."]",16),"Request Error",'routing');
        die();
    }
    
    //###########################################################################
    //If we got this far, hand off to the controllers
    try {
        require_once($include);                                                 //This is where we bring in the controller and the actual action/view processing takes place
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