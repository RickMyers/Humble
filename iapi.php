<?php
/**
       ____      __                        __  _                ___    ____  ____
      /  _/___  / /____  ____ __________ _/ /_(_)___  ____     /   |  / __ \/  _/
      / // __ \/ __/ _ \/ __ `/ ___/ __ `/ __/ / __ \/ __ \   / /| | / /_/ // /
    _/ // / / / /_/  __/ /_/ / /  / /_/ / /_/ / /_/ / / / /  / ___ |/ ____// /
   /___/_/ /_/\__/\___/\__, /_/   \__,_/\__/_/\____/_/ /_/  /_/  |_/_/   /___/
                      /____/

    Supports client/partner integration
 */
/*
 * Brings in the workflow if it exists
 */
function triggerWorkflow($EVENT,$workflow) {
    global $workflowRC;
    global $cancelBubble;
    $flow = 'Workflows/'.$workflow['workflow_id'].'.php';
    if (file_exists($flow)) {
        include($flow);
    }
}
//this function obtained from PHPPro blog.                                      */
function underscoreToCamelCase( $string, $first_char_caps = false) {
    return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
}
/*
 * Dumps anything that any stages appended to the response
 */
function outputResponse() {
    foreach (\Humble::response() as $response) {
        print($response);
    }
}

//##############################################################################
//For the integration point, casts an argument based on how it is specified in
//the configuration section
//##############################################################################
function castArgument($arg,$format='string') {
    switch (strtolower($format)) {
        case 'string'       :
            $arg = (string)$arg;
            break;
        case 'password'     :
            $arg = MD5($arg);
            break;
        case 'int'          :
            $arg = (int)$arg;
            break;
        case 'boolean'      :
            $arg = (bool)$arg;
            break;
        case 'float'        :
            $arg = (float)$arg;
            break;
        case 'json'         :
            $arg = json_decode($arg,true);
            break;
        case 'isodate'      :
            $arg = date('Y-m-d',strtotime($arg));
            break;
        case 'displaydate'  :
            $arg = date('m/d/Y',strtotime($arg));
            break;
        default : break;
    }
    return $arg;
}
//##############################################################################
// Creates an EVENT out of the webservice request
//##############################################################################
function createWorkflowEvent($criteria) {
    $meta             = explode('/',$criteria['uri']);                          //We have to derive the name for the event
    $meta[1]          = (!isset($meta[1])) ? 'Event' : $meta[1];                //If you haven't used a two part [ns/ev] format, the second part of the event name will just be 'Event'
    $event_name       = $meta[0].ucfirst($meta[1]);
    $EVENT            = Event::get($event_name);
    $EVENT->_namespace($meta[0]);
    $EVENT->_component('web-api');
    $EVENT->_method($meta[1]);
    $parameters       = json_decode($criteria['parameters'],true);
    $data             = [];
    if ($parameters) {
        foreach ($parameters as $parameter) {
            switch ($parameter['source']) {
                case 'FILE' :
                    if (isset($_FILES[$parameter['name']]) && file_exists($_FILES[$parameter['name']]['tmp_name'])) {
                        $data[$parameter['name']] = file_get_contents($_FILES[$parameter['name']]['tmp_name']);
                    }
                    break;
                case 'POST' :
                    $data[$parameter['name']] = isset($_POST[$parameter['name']]) ? $_POST[$parameter['name']] : null;
                    break;
                case 'GET' :
                    $data[$parameter['name']] = isset($_GET[$parameter['name']]) ? $_GET[$parameter['name']] : null;
                    break;
                case 'REQUEST' :
                    $data[$parameter['name']] = isset($_REQUEST[$parameter['name']]) ? $_REQUEST[$parameter['name']] : null;
                    break;
                case 'PUT' :
                    break;
                case 'DELETE' :
                    break;
                default :
                    break;
            }
            $data[$parameter['name']] = (isset($parameter['format']) && $data[$parameter['name']]) ? castArgument($data[$parameter['name']],$parameter['format']) :$data[$parameter['name']];
        }
    }
    $action = 'set'.ucfirst($event_name);
    $EVENT->$action($data);
    return $EVENT;
}
//##############################################################################
ob_start();
$session_expire = 300;                  //Expire the session after five minutes#
$workflowRC     = false;
$cancelBubble   = false;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT');
header('Access-Control-Allow-Headers: HTTP_X_REQUESTED_WITH');

chdir('app');
require_once('Humble.php');
require_once('Constants.php');
$request_method  = strtolower($_SERVER['REQUEST_METHOD']);
$error           = false;
$results         = false;
try {
    $URI         = isset($_GET['uri']) ? $_GET['uri'] : false;
    if ($URI) {
        $webservice = Humble::getEntity('paradigm/webservices')->setUri($URI)->load(true);
        if ($webservice) {
            if ($webservice['active']==='Y') {
                $criteria        = Humble::getCollection('paradigm/elements')->setId($webservice['webservice_id'])->load();
                $workflows       = Humble::getEntity('paradigm/webservice_workflows');
                $workflows->setWebserviceId($webservice['id']);
                $workflows->setUri($URI);
                $security_scheme = isset($criteria['choose-security-scheme']) ? $criteria['choose-security-scheme'] : $criteria['security-scheme'];  //@TODO: remove in the future... just use security-scheme
                foreach ($workflows->fetchActiveWebserviceWorkflows() as $workflow) {
                    switch ($security_scheme) {
                        case 'none'     :
                            triggerWorkflow(createWorkflowEvent($criteria),$workflow);
                            break;
                        case 'standard' :
                            $userid     = isset($_REQUEST['userid'])    ? $_REQUEST['userid'] : false;
                            $passwd     = isset($_REQUEST['password'])  ? $_REQUEST['password'] : false;
                            if ($criteria['use-whitelist']==='on') {
                                $allowed = false;
                                foreach (explode("\n",$criteria['whitelist']) as $ip) {
                                    if ($allowed = ($_SERVER['REMOTE_ADDR'] === $ip)) {
                                        break;
                                    }
                                }
                                if (!$allowed) {
                                    header("HTTP/1.1 400 Bad Request");
                                    throw new \Exceptions\WhitelistException('Invalid Requestor',99);
                                    die();
                                }
                            }
                            if ($userid && $passwd) {
                                if (($userid == $criteria['standard-userid']) && ($passwd === $criteria['standard-password'])) {
                                    triggerWorkflow(createWorkflowEvent($criteria),$workflow);
                                } else {
                                    header("HTTP/1.1 400 Bad Request");
                                    throw new \Exceptions\CredentialsIncorrectException('Authorization Information Invalid',24);
                                    die();
                                }
                            } else {
                                header("HTTP/1.1 400 Bad Request");
                                throw new \Exceptions\CredentialsException('Credentials Missing',24);
                                die();
                            }
                            break;
                        case 'session':
                            if ($sessionId = (isset($_POST['sessionId']) ? $_POST['sessionId'] : false)) {
                                session_id($sessionId);
                                session_start();
                                header('Content-Type: application/json');
                                if (isset($_SESSION['uid']) && isset($_SESSION['user'])) {
                                    if (isset($_SESSION['began'])) {
                                        if ((time() - (int)$_SESSION['began']) > $session_expire) {
                                            session_destroy();
                                            session_write_close();
                                            \Humble::response(json_encode([
                                                "RC" => "12",
                                                "message" => "Session Expired",
                                                "description" => "Five minutes after the last authentication call the current session will expire",
                                                "remedy" => "Please call the authentication service again to recreate your session"
                                            ],JSON_PRETTY_PRINT));
                                            outputResponse();
                                            die();
                                        }
                                    }
                                    triggerWorkflow(createWorkflowEvent($criteria),$workflow);
                                } else {
                                    header("HTTP/1.1 400 Bad Request");
                                    \Humble::response(json_encode([
                                        "RC" => "12",
                                        "message" => "Invalid Session ID",
                                        "description" => "The session ID either expired or is incorrect",
                                        "remedy" => "Obtain a new session ID by accessing the authentication service"
                                    ],JSON_PRETTY_PRINT));
                                    outputResponse();
                                    die();
                                }
                            } else {
                                header("HTTP/1.1 400 Bad Request");
                                \Humble::response(json_encode([
                                    "RC" => "12",
                                    "message" => "Missing Session ID",
                                    "description" => "The session ID was not present in the POST",
                                    "remedy" => "Make sure you are passing the SessionID in the POST variable 'sessionId'"
                                ],JSON_PRETTY_PRINT));
                                outputResponse();
                                die();
                            }
                            break;
                        case 'api'  :
                            break;
                        default:
                            print('Unknown API Security Scheme: ['.$security_scheme.']');
                    }
                }
                outputResponse();
            } else {
                header("HTTP/1.1 400 Bad Request");
                throw new \Exceptions\DisabledWebServiceException('Disabled Web Service',8);
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            throw new \Exceptions\UnknownWebServiceException('WebService Not Found',16);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        throw new \Exceptions\MissingURIException('URI Invalid or Not Set',24);
    }
} catch (\Exceptions\WhitelistException $e) {
    HumbleException::standard($e,'IP Address Not On Approved List','integration');
} catch (\Exceptions\CredentialsIncorrectException $e) {
    HumbleException::standard($e,'Credentials Incorrect','integration');
} catch (\Exceptions\CredentialsException $e) {
    HumbleException::standard($e,'Missing Credentials','integration');
} catch (\Exceptions\DisabledWebServiceException $e) {
    HumbleException::standard($e,'Disabled Web Service','integration');
} catch (\Exceptions\MissingURIException $e) {
    HumbleException::standard($e,'URI Missing','integration');
} catch (\Exceptions\UnknownWebServiceException $e) {
    HumbleException::standard($e,'Unknown Webservice','integration');
} catch (\Exceptions\HumbleException $e) {
    HumbleException::standard($e, "WebService  Error");
} catch (Exception $e) {
    HumbleException::standard($e, "General Error");
}
?>