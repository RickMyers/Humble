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
$pid                        = getmypid();
$offset_time                = 0;
$cadence_ctr                = 0;
$last_run                   = [];
$is_production              = false;

//------------------------------------------------------------------------------
//Load custom callbacks if any
//------------------------------------------------------------------------------
if (file_exists('CALLBACKS.php')) {
    require_once('CALLBACKS.php');
}

//------------------------------------------------------------------------------
function scanControllersForChanges($last_run=false) {
    global $is_production;
    print("Scanning Controllers...\n\n");
    $controllers = Humble::getEntity('humble/modules');
    print_r($contollers); die();
            
    $controllers = Humble::getEntity('humble/controllers')->orderBy('namespace=ASC')->fetch();
    print_r($controllers); die();
    sleep(2);
}

//------------------------------------------------------------------------------
function scanModelsForChanges() {
    global $is_production;
    if (!$is_production) {
        print("Scanning Models...\n\n");
        sleep(1);
    }
}

//------------------------------------------------------------------------------
function scanFilesForChanges() {
    print("Scanning Files...\n\n");
    sleep(1);
}

//------------------------------------------------------------------------------
function triggerWorkflows() {
    print("Triggering Workflows...\n\n");
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
        $is_production  = \Environment::isProduction();
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
        $offset_time += time() - $duration;                                     //We 
        print('Offset: '.$offset_time."\n");
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


