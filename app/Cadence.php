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


function fileScan() {
    
}
function workflowCheck() {
    
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
if (file_exists('cadence.json') && ($cadence = json_decode('cadence.json'))) {

    while (file_exists('cadence.pid')) {

    }

} else {
    print('Cadence is not configured, please see https://humbleprogramming.com/pages/Cadence.htmls for instructions on how to configure the service'."\n\n");
}
@unlink('cadence.pid');
