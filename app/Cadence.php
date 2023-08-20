<?php
/*
   ______          __                   
  / ____/___ _____/ /__  ____  ________ 
 / /   / __ `/ __  / _ \/ __ \/ ___/ _ \
/ /___/ /_/ / /_/ /  __/ / / / /__/  __/
\____/\__,_/\__,_/\___/_/ /_/\___/\___/ 
                                        
Let's just do something every so often...
 
 */
require_once "Humble.php";

$started                    = time();                                           //The time used in all offset calculations
$pid                        = getmypid();                                       //My process ID
$offset_time                = 0;                                                //The cumulative time spent doing stuff
$cadence_ctr                = 0;                                                //Lets count the beat
$last_run                   = [];                                               //A collection of times when something was last done
$is_production              = false;                                            //Are you in production?  Some stuff is turned off if so
$compiler                   = false;

//------------------------------------------------------------------------------
//Load custom callbacks if any
//------------------------------------------------------------------------------
if (file_exists('CALLBACKS.php')) {
    require_once('CALLBACKS.php');
}

function logMessage($stuff=false) {
    global $cadence;
    if ($stuff && isset($cadence['log']['location'])) {
        if (isset($cadence['log']['max_size'])) {
            
        }
        $stuff = is_object($stuff) ? print_r($stuff,true) : $stuff;
        if (true) {
            file_put_contents($cadence['log']['location'],$stuff,FILE_APPEND);
        } else {
            
        }
        print($stuff."\n");
    }
}
//------------------------------------------------------------------------------
function scanControllersForChanges($last_run=false) {
    global $compiler,$is_production;
    if (!$is_production) {
        logMessage("Scanning Controllers...\n\n");
        $compiler    = false;
        $scan_start  = time();
        $namespaces  = [];
        $controllers = Humble::getEntity('humble/controllers')->orderBy('namespace=ASC')->fetch();
        foreach ($controllers as $idx => $metadata) {
            if ($ns     = $namespaces[$metadata['namespace']] = isset($namespaces[$metadata['namespace']]) ? $namespaces[$metadata['namespace']] : Humble::getModule($metadata['namespace'])) {
                $file   = 'Code/'.$ns['package'].'/'.$ns['controller'].'/'.$metadata['controller'].'.xml';
                if (file_exists($file) && ($ft = filemtime($file))) {
                    if ($ft !== ($st = strtotime($metadata['compiled']))) {
                       print('Going to compile '.$file." [".$ft."/".$st."]\n");
                       $compiler   = ($compiler) ? $compiler : \Environment::getCompiler();
                       $compiler->compile($metadata['namespace'].'/'.$metadata['controller']);
                    }
                }
            } else {
                logMessage($metadata);
                logMessage("Namespace ". $metadata['namespace']." found but not valid\n");
            }
        } 
        logMessage("Controller Scan took ".($scan_start-time())." seconds\n");
        sleep(2);
    }
}

//------------------------------------------------------------------------------
function scanModelsForChanges() {
    global $is_production;
    if (!$is_production) {
        logMessage("Scanning Models...\n\n");
        sleep(1);
    }
}

//------------------------------------------------------------------------------
function scanFilesForChanges() {
    logMessage("Scanning Files...\n\n");
    sleep(1);
}

//------------------------------------------------------------------------------
function triggerWorkflows() {
    logMessage("Triggering Workflows...\n\n");
    sleep(2);
}

//--------------------------------------------------------------------------------------------------------------------------------------------
//We are not going to want more than one instance of cadence running per application, so we are going to record the current running PID
//  of cadence, and make sure that it stays that value during running, if it changes or is deleted, we are going to abort processing
//
if (file_exists('cadence.pid') && ($running_pid = trim(file_get_contents('cadence.pid')))) {
    if ($pid !== $running_pid) {
        die("Cadence appears to be running already.  If not, you may need to manually delete the cadence.pid file before trying again\n\n");
    }
} else {
    file_put_contents('cadence.pid',$pid);                                      //alright, let's record your process number
}

//--------------------------------------------------------------------------------------------------------------------------------------------
//Check for configuration file, which configures how period for the cadence, and when to do which checks...
//
if (file_exists('cadence.json') && ($cadence = json_decode(file_get_contents('cadence.json'),true))) {
    print("Starting Cadence...\n\n");
    while (file_exists('cadence.pid') && ((int)file_get_contents('cadence.pid')===$pid)) {
        sleep($cadence['period']);
        $is_production  = \Environment::isProduction();                         //must do in loop since someone can change this in the admin panel at any time
        $duration       = time();
        $now            = time() - $offset_time - $started;
        foreach ($cadence['handlers'] as $component => $handler) {
            $t = $handler['multiple'] * $cadence['period'];
            if (($now % $t) == 0) {
                foreach ($handler['callbacks'] as $callback) {
                    
                    $callback();
                }
            }
        }
        $offset_time += (time() - $duration);                                   //We 
        if ($cadence_ctr++ > 50) {
            print("Reseting Cadence...\n");
            $started        = time();
            $offset_time    = 0;
            $cadence_ctr    = 0;
        }
        
    }
    @unlink('cadence.pid');
    die("\n\nAborting due to PID file being deleted or PID changed...\n\n");
}
print("\nCadence is not configured, please see https://humbleprogramming.com/pages/Cadence.htmls for instructions on how to configure the service.\n\n");


