<?php
/**
    __        __   _     _   _             _         _    ____ ___ 
    \ \      / /__| |__ | | | | ___   ___ | | __    / \  |  _ \_ _|
     \ \ /\ / / _ \ '_ \| |_| |/ _ \ / _ \| |/ /   / _ \ | |_) | | 
      \ V  V /  __/ |_) |  _  | (_) | (_) |   <   / ___ \|  __/| | 
       \_/\_/ \___|_.__/|_| |_|\___/ \___/|_|\_\ /_/   \_\_|  |___|
                                                                
    Supports webhook calls from external APIs
*/

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

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT');
header('Access-Control-Allow-Headers: HTTP_X_REQUESTED_WITH');

chdir('app');
require_once('Humble.php');
require_once('Constants.php');
$request_method  = strtolower($_SERVER['REQUEST_METHOD']);
$error           = false;
$results         = false;
print_r($_GET);
$namespace       = isset($_GET['n'])    ? $_GET['n'] : false;
$hook            = isset($_GET['hook']) ? $_GET['hook'] : false;
print($namespace.'/'.$hook);
if ($namespace && $hook) {
    if ($events = Humble::entity('paradigm/webhook/workflows')->setNamespace($namespace)->setHook($hook)->setActive('Y')->load(true)) {
        print_r($events);
            /*
             * o Turn hook into an event
             * 
             */

    }
}