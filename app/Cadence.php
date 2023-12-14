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
$cadence                    = [];                                               //The JSON stored instructions for what to run and when
$compiler                   = false;                                            //Singleton reference to the controller compiler
$files                      = [];                                               //These are the loose files to watch
$models                     = [];                                               //These are the models in the modules to watch
$configs                    = [];                                               //These are the configuration files in the modules to watch
$systemfiles                = [];                                               //These are the loose files belonging to the framework to watch
$images                     = [];                                               //These are images contained in the image folder
$modules                    = Humble::entity('humble/modules')->setEnabled('Y')->fetch();
$monitor                    = \Environment::getMonitor();                       //System monitor for checking on performanc
$updater                    = \Environment::getUpdater();                       //Singleton reference to the module updater
$installer                  = \Environment::getInstaller();                     //Singleton reference to the module installer
$is_production              = \Environment::isProduction();                     //Am I in production? Somethings will be skipped if so

//------------------------------------------------------------------------------
//Load custom callbacks if any
//------------------------------------------------------------------------------
if (file_exists('CALLBACKS.php')) {
    require_once('CALLBACKS.php');
}

//------------------------------------------------------------------------------
function resetCadence() {
    global $started, $pid, $offset_time, $cadence, $cadence_ctr,$files, $models, $configs, $systemfiles;
    $started                = time();                                           //The time used in all offset calculations
    $pid                    = getmypid();                                       //My process ID
    $offset_time            = 0;                                                //The cumulative time spent doing stuff
    $cadence_ctr            = 0;                                                //Lets count the beat
    $files                  = [];
    $models                 = [];
    $configs                = [];
    $systemfiles            = [];    
    $cadence                = json_decode(file_get_contents('cadence.json'),true);
    $is_production          = \Environment::isProduction();
}
//------------------------------------------------------------------------------
function calcMaxFileSize($value='1M') {
    $maxSize = 1000000;  //default
    $options = [
        'K'  => 1000,
        'M'  => 1000000,
        'G'  => 100000000
    ];
    $unit    = strtoupper(substr($value,-1));
    if (isset($options[$unit])) {
        $maxSize = substr($value,0,strlen($value)-1) * $options[$unit];
    }
    return $maxSize;
}
//------------------------------------------------------------------------------
function logMessage($message=false,$timestamp=true) {
    global $cadence;
    if ($message && isset($cadence['log']['location'])) {
        if (!file_exists($cadence['log']['location'])) {
            file_put_contents($cadence['log']['location'],'');
        }
        $message    = (($timestamp) ? '['.date('Y-m-d H:i:s').'] - ' : '').$message."\n";
        $overwrite  = isset($cadence['log']['max_size']) && filesize($cadence['log']['location']) > calcMaxFileSize($cadence['log']['max_size']);
        if ($overwrite) {
            file_put_contents($cadence['log']['location'],$message);
        } else {
            file_put_contents($cadence['log']['location'],$message,FILE_APPEND);
        }
        //print($message);
    }
}
//------------------------------------------------------------------------------
function scanControllersForChanges($last_run=false) {
    global $compiler,$is_production;
    if (!$is_production) {
        $compiler    = false;
        $namespaces  = [];
        foreach (Humble::entity('humble/controllers')->orderBy('namespace=ASC')->fetch() as $idx => $metadata) {
            if ($ns     = $namespaces[$metadata['namespace']] = isset($namespaces[$metadata['namespace']]) ? $namespaces[$metadata['namespace']] : Humble::module($metadata['namespace'])) {
                $file   = 'Code/'.$ns['package'].'/'.$ns['controller'].'/'.$metadata['controller'].'.xml';
                if (file_exists($file) && ($ft = filemtime($file))) {
                    if ($ft !== ($st = strtotime($metadata['compiled']))) {
                       logMessage('Going to compile '.$file." [".$ft."/".$st."]");
                       $compiler   = ($compiler) ? $compiler : \Environment::getCompiler();
                       $compiler->compile($metadata['namespace'].'/'.$metadata['controller']);
                    }
                }
            } else {
                logMessage("Namespace ". $metadata['namespace']." found but not valid");
            }
        } 
    }
}

//------------------------------------------------------------------------------
function recurseDirectory($dir=[]) {
    $list = [];
    $dh = dir($dir);
    while ($entry = $dh->read()) {
        if (($entry == '.') || ($entry == '..')) {
            continue;
        }
        if (is_dir($dir.'/'.$entry)) {
            array_merge($list,recurseDirectory($dir.'/'.$entry));
        } else {
            $list[] = $dir.'/'.$entry;
        }
    }
    return $list;
}
//------------------------------------------------------------------------------
function scanModelsForChanges() {
    global $is_production,$models,$installer,$modules;
    if (!$is_production) {
        foreach ($modules as $module) {
            if ($module['namespace']=='humble') {
                logMessage("Skipping The Humble Module, if you need to scan this module do it manually...");
                continue;
            }
            $files[$module['namespace']] = recurseDirectory('Code/'.$module['package'].'/'.$module['models']);
            foreach ($files[$module['namespace']] as $file) {
                if ($file == 'Code/Framework/Humble/Models/MySQL.php') {
                    logMessage('Skipping MySQL Model');
                    continue;
                }
                if (isset($models[$file])) {
                    if ($models[$file] !== filemtime($file)) {
                        print("Scanning ".$models[$file]."\n");
                        try {
                            $installer->registerWorkflowComponents($module['namespace']);
                        } catch (Exception $ex) {
                            print_r($ex);
                        }
                        $models[$file] = filemtime($file);
                    }
                } else {
                    $models[$file] = filemtime($file);
                }
            }
        }
    }
}
//------------------------------------------------------------------------------
function watchApplicationXML() {
    global $systemfiles;
    $systemfiles['../application.xml'] = isset($systemfiles['../application.xml']) ? $systemfiles['../application.xml'] : filemtime('../application.xml');
    if (filemtime('../application.xml') !== $systemfiles['../application.xml']) {
        logMessage('Recaching Application.xml');
        \Environment::recacheApplication();
    }
}
//------------------------------------------------------------------------------
function watchAPIPolicy() {
    global $systemfiles;
    $systemfiles['api_policy.json'] = isset($systemfiles['api_policy.json']) ? $systemfiles['api_policy.json'] : filemtime('api_policy.json');
    if (filemtime('api_policy.json') !== $systemfiles['api_policy.json']) {
        logMessage('Recaching API Policy');
        Humble::cache('humble_framework_api_policy',json_decode(file_get_contents('api_policy.json')));
    }    
}
//------------------------------------------------------------------------------
function watchAllowedRules() {
    global $systemfiles;
    $systemfiles['allowed.json'] = isset($systemfiles['allowed.json']) ? $systemfiles['allowed.json'] : filemtime('allowed.json');
    if (filemtime('allowed.json') !== $systemfiles['allowed.json']) {
        logMessage('Recaching Allowed Routes');
        Humble::cache('humble_framework_allowed_routes',json_decode(file_get_contents('allowed.json')));
    }        
}
//------------------------------------------------------------------------------
function scanConfigurationsForChanges() {
    global $configs,$updater,$modules;
    foreach ($modules as $module) {
        $file = 'Code/'.$module['package'].'/'.$module['configuration'].'/config.xml';
        $configs[$file] = $configs[$file] ?? filemtime($file);
        if ($configs[$file] !== filemtime($file)) {
            $configs[$file] = filemtime($file);
            logMessage('Configuration file change detected, updating '.$file);
            ob_start();
            try {
                $updater->update($file);
            } catch (Exception $ex) {
                logMessage('Error trying to update module '.$module['namespace']);
                logMessage($ex->getTraceAsString());
            }
            logMessage(ob_end_clean(),false);
        }
    }
}
//------------------------------------------------------------------------------
function scanForNewImages() {
    global $images,$modules;
}
//------------------------------------------------------------------------------
function scanFilesForChanges() {
    global $files,$modules;
}
//Are these two the same?
//------------------------------------------------------------------------------
function triggerFileWorkflows() {
}
// To spin off a process in another thread... 'nohup php Program.php > /dev/null &'
//------------------------------------------------------------------------------
function processCadenceCommand($cmds) {
    foreach ($cmds as $cmd) {
        switch (strtoupper($cmd)) {
            case 'RESTART'  :
                //There are problems with restart... the original thread doesn't term, so you get 2 instances of Cadence
                //Maybe look into ending this thread by writing instructions to a file and have a cron job restart
                //Cadence if it sees that file.  Maybe run every 10 seconds or so
                @unlink('cadence.pid');
                logMessage('Restarting Cadence...');
                exec('nohup php Cadence.php > /dev/null &');
                die();
                break;
            case 'RELOAD'   :
                resetCadence();
                break;
            case 'END'      :
            case 'STOP'     :
                @unlink('cadence.pid');
                logMessage('Ending Cadence...');
                die('Aborting Cadence'."\n\n");
                break;
            default         :
                break;
        }
    }
}

//--------------------------------------------------------------------------------------------------------------------------------------------
//We are not going to want more than one instance of cadence running per application, so we are going to record the current running PID
//  of cadence, and make sure that it stays that value during running, if it changes or is deleted, we are going to abort processing
//
if (file_exists('cadence.pid') && ($running_pid = trim(file_get_contents('cadence.pid')))) {
    if ($pid !== $running_pid) {
        die("\nCadence appears to be running already.  If not, you may need to manually delete the cadence.pid file before trying again\n\n");
    }
} else {
    file_put_contents('cadence.pid',$pid);                                      //alright, let's record your process number
}

//--------------------------------------------------------------------------------------------------------------------------------------------
//Check for configuration file, which configures how period for the cadence, and when to do which checks...
//
if (file_exists('cadence.json') && ($cadence = json_decode(file_get_contents('cadence.json'),true))) {
    logMessage("Starting Cadence...");
    while (file_exists('cadence.pid') && ((int)file_get_contents('cadence.pid')===$pid)) {
        sleep($cadence['period']);
        if (file_exists('cadence.cmd') && ($cmds = json_decode(file_get_contents('cadence.cmd')))) {
            unlink('cadence.cmd');            
            processCadenceCommand($cmds);
        }
        $duration       = time();
        $now            = time() - $offset_time - $started;
        foreach ($cadence['handlers'] as $component => $handler) {
            $t = $handler['multiple'] * $cadence['period'];
            if (($now % $t) == 0) {
                $start  = time();
                logMessage("Processing ".ucfirst($component)." Now...");
                foreach ($handler['callbacks'] as $callback => $status) {
                    if ($status === true) {
                        $callback();
                    }
                }
                logMessage(ucfirst($component)." Processing took ".($start - time())." seconds");                
            }
        }
        $offset_time += (time() - $duration);                                   
        if ($cadence_ctr++ > 50) {
            logMessage("Reseting Cadence...");                                     //Due to "fuzziness" caused by sleep/awake timer, we need to periodically reset counters
            $started        = time();
            $offset_time    = 0;
            $cadence_ctr    = 0;
        }
        
    }
    @unlink('cadence.pid');
    die("\n\nAborting due to PID file being deleted or PID changed...\n\n");
}
print("\nCadence is not configured, please see https://humbleprogramming.com/pages/Cadence.htmls for instructions on how to configure the service.\n\n");


