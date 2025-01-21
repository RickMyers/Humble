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
$first_time                 = [
    'images'                => true
];
$cadence_ctr                = 0;                                                //Lets count the beat
$cadence                    = [];                                               //The JSON stored instructions for what to run and when
$compiler                   = false;                                            //Singleton reference to the controller compiler
$files                      = [];                                               //These are the loose files to watch
$models                     = [];                                               //These are the models in the modules to watch
$configs                    = [];                                               //These are the configuration files in the modules to watch
$systemfiles                = [];                                               //These are the loose files belonging to the framework to watch
$images                     = [];                                               //These are images contained in the image folder
$modules                    = Humble::entity('humble/modules')->setEnabled('Y')->fetch();
$system                     = Humble::entity('admin/system/monitor');
$monitor                    = \Environment::getMonitor();                       //System monitor for checking on performanc
$updater                    = \Environment::getUpdater();                       //Singleton reference to the module updater
$installer                  = \Environment::getInstaller();                     //Singleton reference to the module installer
$is_production              = \Environment::isProduction();                     //Am I in production? Somethings will be skipped if so
$project                    = \Environment::getProject();
$config                     = (\Environment::namespace() !== 'humble') ? 'Code/'.$project->package.'/'.$project->module.'/etc/cadence.json' : 'Code/Framework/Humble/etc/application.json';
$callbacks                  = 'Code/'.$project->package.'/'.$project->module.'/includes/Cadence.php';
$constants                  = 'Code/'.$project->package.'/'.$project->module.'/includes/Constants.php';
$framework                  = 'Code/Framework/Humble/etc/cadence.json';

if (file_exists($constants)) {
    require_once($constants);
}

//------------------------------------------------------------------------------
//Load custom callbacks if any
//------------------------------------------------------------------------------
if (file_exists($callbacks)) {
    require_once($callbacks);
}

//------------------------------------------------------------------------------
function resetCadence() {
    global $started, $pid, $cadence, $cadence_ctr,$files, $models, $configs, $systemfiles, $config, $images, $first_time;
    $started                = time();                                           //The time used in all offset calculations
    $pid                    = getmypid();                                       //My process ID
    $cadence_ctr            = 0;                                                //Lets count the beat
    $files                  = [];
    $models                 = [];
    $images                 = [];
    $first_time             = [
        'images'            => true,
        'models'            => true,
        'configs'           => true,
        'system'            => true
    ];
    $configs                = [];
    $systemfiles            = [];    
    $cadence                = json_decode(file_get_contents($config),true);
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
        print($message);
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
                       logMessage('---------> Going to compile '.$file." [".$ft."/".$st."]");
                       $compiler   = ($compiler) ? $compiler : \Environment::getCompiler();
                       $compiler->compile($metadata['namespace'].'/'.$metadata['controller']);
                    }
                    clearstatcache(true,$file);
                }
            } else {
                logMessage("Namespace ". $metadata['namespace']." Found But Not Valid");
            }
        } 
    }
}
//------------------------------------------------------------------------------
function clearSystemStats() {
    global $monitor;
    logMessage('Clearing System Monitoring Data Over Two Weeks Old');
    $monitor->clear();
}
//------------------------------------------------------------------------------
function snapshotSystem() {
    global $monitor, $system;
    logMessage('Taking A Snapshot Of System Statistics');
    $monitor->record();
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
function scanModel($file=false,$namespace=false) {
    global $installer;
    if ($file && $namespace) {
        logMessage("Scanning ".$file."\n");
        try {
            $installer->registerWorkflowComponents($namespace);
        } catch (Exception $ex) {
            logMessage($ex->getCode().': '.$ex->getMessage());
        }        
    }
}
//------------------------------------------------------------------------------
function primeModelsArray() {
    global $modules, $models;
    foreach ($modules as $module) {
        foreach ($files = recurseDirectory('Code/'.$module['package'].'/'.$module['models']) as $file) {
            $models[$file] = filemtime($file);
            clearstatcache(true,$file);
        }
    }
}
//------------------------------------------------------------------------------
function scanModelsForChanges() {
    global $is_production,$models,$installer,$modules,$first_time;
    if ($first_time['models']) {
        primeModelsArray();
    }
    if (!$is_production) {
        foreach ($modules as $module) {
            if ($module['namespace']=='humble') {
                //Due to criticality of components, scanning can cause an abend, so do manual scans of this module
                continue;
            }
            $files[$module['namespace']] = recurseDirectory('Code/'.$module['package'].'/'.$module['models']);
            foreach ($files[$module['namespace']] as $file) {
                if ($file == 'Code/Framework/Humble/Models/MySQL.php') {
                    //This causes an abend
                    continue;
                }
                if (isset($models[$file])) {
                    if ($models[$file] !== filemtime($file)) {
                        scanModel($file,$module['namespace']);
                        $models[$file] = filemtime($file);
                    }
                } else {
                    scanModel($file,$module['namespace']);
                    $models[$file] = filemtime($file);
                }
                clearstatcache(true,$file);
            }
        }
        $first_time['models'] = false;
    }
}
//------------------------------------------------------------------------------
function watchApplicationXML() {
    global $systemfiles;
    $applicationXML = Environment::applicationXMLLocation();
    $appTime        = filemtime($applicationXML);
    $systemfiles[$applicationXML] = isset($systemfiles[$applicationXML]) ? $systemfiles[$applicationXML] : $appTime;
    if ($appTime !== $systemfiles[$applicationXML]) {
        logMessage('---------> Recaching Application.xml');
        \Environment::recacheApplication();
        $systemfiles[$applicationXML] = $appTime;
    }
    clearstatcache(true,$applicationXML);
}
//------------------------------------------------------------------------------
function watchAPIPolicy() {
    global $systemfiles, $project;
    $api_policy = 'Code/'.$project->package.'/'.$project->module.'/etc/api_policy.json';
    $api_time   = filemtime($api_policy);
    $systemfiles['api_policy.json'] = isset($systemfiles['api_policy.json']) ? $systemfiles['api_policy.json'] : $api_time;
    if ($api_time !== $systemfiles['api_policy.json']) {
        logMessage('---------> Recaching API Policy');
        Humble::cache('humble_framework_api_policy',json_decode(file_get_contents($api_policy)));
        $systemfiles['api_policy.json'] = $api_time;
    } 
    clearstatcache(true,$api_policy);
}
//------------------------------------------------------------------------------
function watchAllowedRules() {
    global $systemfiles, $project;
    $public_routes = 'Code/'.$project->package.'/'.$project->module.'/etc/public_routes.json';    
    $routes_time   = filemtime($public_routes);
    $systemfiles['public_routes.json'] = isset($systemfiles['public_routes.json']) ? $systemfiles['public_routes.json'] : $routes_time;
    if (filemtime($public_routes) !== $systemfiles['public_routes.json']) {
        logMessage('---------> Recaching Public Routes');
        Humble::cache('humble_framework_allowed_routes',json_decode(file_get_contents($public_routes)));
        $systemfiles['public_routes.json'] = $routes_time;
    }        
    clearstatcache(true,$routes_time);
}
//------------------------------------------------------------------------------
function scanConfigurationsForChanges() {
    global $configs,$updater,$modules;
    foreach ($modules as $module) {
        $file = 'Code/'.$module['package'].'/'.$module['configuration'].'/config.xml';
        $configs[$file] = $configs[$file] ?? filemtime($file);
        if ($configs[$file] !== filemtime($file)) {
            $configs[$file] = filemtime($file);
            logMessage('---------> Configuration file change detected, updating '.$file);
            ob_start();
            try {
                $updater->update($file);
            } catch (Exception $ex) {
                logMessage('---=====> Error trying to update module '.$module['namespace']);
                logMessage($ex->getTraceAsString());
            }
            logMessage(ob_end_clean(),false);
        }
        clearstatcache(true,$file);
    }
}
//------------------------------------------------------------------------------
function primeImagesArray() {
    global $modules, $images;
    foreach ($modules as $module) {
        foreach ($files = recurseDirectory('Code/'.$module['package'].'/'.$module['images']) as $file) {
            $images[$file] = filemtime($file);
            clearstatcache(true,$file);
        }
    }
}
//------------------------------------------------------------------------------
function scanImagesForChanges() {
    global $images,$modules,$first_time;
    if ($first_time['images']) {
        primeImagesArray();
    }
    $files = [];
    foreach ($modules as $module) {
        $files[$module['namespace']] = recurseDirectory('Code/'.$module['package'].'/'.$module['images']);  
        foreach ($files[$module['namespace']] as $file) {
            //$images[$file] = $images[$file] ?? filemtime($file);
            if (!isset($images[$file]) || ($images[$file] !== filemtime($file))) {
                //this is a new or updated file, must copy over
                $parts = explode('/',$file);
                $dest = '../images/'.$module['namespace'];
                for ($i=4; $i<count($parts); $i++) {
                    $dest .= '/'.$parts[$i];
                }
                logMessage('--------> Copying image '.$file.' to '.$dest);
                copy($file,$dest);
                $images[$file] = filemtime($file);
            }
            clearstatcache(true,$file);
        }
    }
    $first_time['images'] = false;
}
//------------------------------------------------------------------------------
function scanFilesForChanges() {
    global $files,$modules;
    $triggers = ['changed'=>[],'new'=>[]];
    foreach (Humble::entity('paradigm/file/triggers')->setActive('Y')->fetch() as $trigger) {
        if (is_dir($trigger['directory'])) {
            $dir        = dir($trigger['directory']);
            $extension  = $trigger['extension'] ? str_replace(['*','.'],['',''],$trigger['extension']): false;
            // print_r($extension."\n"); unlink('PIDS/cadence.pid'); die();
            while ($entry = $dir->read()) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if ($extension) {
                    if (substr($entry,-1*strlen($extension))!==$extension) {
                        continue;
                    }
                }
                $file      = $trigger['directory'].'/'.$entry;
                $file_time = filemtime($file);
                if (!isset($files[$file])) {
                    $triggers['new'][$file] = $trigger;
                    logMessage('Found a new file '.$file);
                } else if ($file_time !== $files[$file]) {
                    $triggers['changed'][$file] = $trigger;
                    logMessage('Detected a changed file '.$file);
                }
                $files[$file]    = $file_time;
            }
        } else {
            logMessage("Can't scan trigger directory ".$trigger['directory']." because it doesn't exist!");
        }
    }
    if (count($triggers['new']) || count($triggers['changed'])) {
        triggerFileWorkflows($triggers);
    }
}
//------------------------------------------------------------------------------
function triggerFileWorkflows($triggers=[]) {
   
    $job = Humble::entity('paradigm/job/queue');
    foreach ($triggers['new'] as $file => $trigger) {
        $job->setWorkflowId($trigger['workflow_id'])->setQueued(date('Y-m-d H:i:s'))->setFilename($file)->setFileAction('new')->setStatus(NEW_FILE_JOB)->save();
    }
    foreach ($triggers['changed'] as $file => $trigger) {
        $job->setWorkflowId($trigger['workflow_id'])->setQueued(date('Y-m-d H:i:s'))->setFilename($file)->setFileAction('change')->setStatus(NEW_FILE_JOB)->save();
    }
    Humble::model('paradigm/system')->runFileLauncher();
     print_r($triggers);unlink('PIDS/cadence.pid');die();
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
                @unlink('PIDS/cadence.pid');
                logMessage('Restarting Cadence...');
                exec('nohup php Cadence.php > /dev/null &');
                die();
                break;
            case 'RELOAD'   :
                resetCadence();
                break;
            case 'DUMP'     :
                //dump memory contents to a file
                break;
            case 'END'      :
            case 'STOP'     :
                @unlink('PIDS/cadence.pid');
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
if (file_exists('PIDS/cadence.pid') && ($running_pid = trim(file_get_contents('PIDS/cadence.pid')))) {
    if ($pid !== $running_pid) {
        die("\nCadence appears to be running already.  If not, you may need to manually delete the cadence.pid file before trying again\n\n");
    }
} 
file_put_contents('PIDS/cadence.pid',$pid);                                     //alright, let's record your process number


//--------------------------------------------------------------------------------------------------------------------------------------------
//Check for configuration file, which configures how period for the cadence, and when to do which checks...
//
if (file_exists($config) || file_exists($framework)) {
    $cadence        = file_exists($config) ? json_decode(file_get_contents($config),true) : die('no config');
    $application    = file_exists($framework) ? json_decode(file_get_contents($framework),true): [];
    $cadence        = array_merge_recursive($application,$cadence);
}
if ($cadence) {
//    print_r($cadence); unlink('PIDS/cadence.pid'); die();
    logMessage("Starting Cadence...");
    while (file_exists('PIDS/cadence.pid') && ((int)file_get_contents('PIDS/cadence.pid')===$pid)) {
        sleep($cadence['period']);
        logMessage('Waking...');
        $duration       = time();
        if (file_exists('cadence.cmd') && ($cmds = json_decode(file_get_contents('cadence.cmd')))) {
            unlink('cadence.cmd');            
            processCadenceCommand($cmds);
        }
        $handlers = array_merge($cadence['handlers']['framework'],$cadence['handlers']['application']);
        foreach ($handlers as $component => $handler) {
            $t = $handler['multiple'] * $cadence['period'];
            if (($cadence_ctr % $t) == 0) {
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
        if (($cadence_ctr++ > 500)) {
            logMessage("Reseting Cadence...");                                     //Due to "fuzziness" caused by sleep/awake timer, we need to periodically reset counters
            $started        = time();
            $cadence_ctr    = 0;
        }
        
        logMessage('This run took '.date('s',time()-$duration).' seconds');
        logMessage('Sleeping for '.$cadence['period'].' seconds');
    }
    @unlink('PIDS/cadence.pid');
    die("\n\nAborting due to PID file being deleted or PID changed...\n\n");
}
print("\nCadence is not configured, please see https://humbleprogramming.com/pages/Cadence.htmls for instructions on how to configure the service.\n\n");


