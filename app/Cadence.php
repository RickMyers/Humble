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
$is_production              = false;                                            //Are you in production?  Some stuff is turned off if so
$compiler                   = false;
$files                      = [];
$models                     = [];
$configs                    = [];
$systemfiles                = [];
$installer                  = \Environment::getInstaller();

//------------------------------------------------------------------------------
//Load custom callbacks if any
//------------------------------------------------------------------------------
if (file_exists('CALLBACKS.php')) {
    require_once('CALLBACKS.php');
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
function logMessage($message=false) {
    global $cadence;
    if ($message && isset($cadence['log']['location'])) {
        if (!file_exists($cadence['log']['location'])) {
            file_put_contents($cadence['log']['location'],'');
        }
        $message    = '['.date('Y-m-d H:i:s').'] - '.$message."\n";
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
        $controllers = Humble::getEntity('humble/controllers')->orderBy('namespace=ASC')->fetch();
        foreach ($controllers as $idx => $metadata) {
            if ($ns     = $namespaces[$metadata['namespace']] = isset($namespaces[$metadata['namespace']]) ? $namespaces[$metadata['namespace']] : Humble::getModule($metadata['namespace'])) {
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
    global $is_production,$models,$installer;
    if (!$is_production) {
        foreach (Humble::getEntity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            if ($module['namespace']=='humble') {
                print("Skipping Humble...\n\n");
                continue;
            }
            $files[$module['namespace']] = recurseDirectory('Code/'.$module['package'].'/'.$module['models']);
            foreach ($files[$module['namespace']] as $file) {
                if ($file == 'Code/Base/Humble/Models/MySQL.php') {
                    print("Skipping MySQL\n\n");
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
    if (filemtime('../application.xml')!==$systemfiles['../application.xml']) {
        //recache application.xml
    }
}
//------------------------------------------------------------------------------
function watchAPIPolicy() {
    global $systemfiles;
    $systemfiles['api_policy.json'] = isset($systemfiles['api_policy.json']) ? $systemfiles['api_policy.json'] : filemtime('api_policy.json');
    if (filemtime('api_policy.json')!==$systemfiles['api_policy.json']) {
        //recache api_policy.json
    }    
}
//------------------------------------------------------------------------------
function watchAllowedRules() {
    global $systemfiles;
    $systemfiles['allowed.json'] = isset($systemfiles['allowed.json']) ? $systemfiles['alowed.json'] : filemtime('allowed.json');
    if (filemtime('allowed.json')!==$systemfiles['allowed.json']) {
        //recache allowed.json
    }        
}
//------------------------------------------------------------------------------
// Callback to watch application.xml and recache
// Callback to watch api_policy.json and recache
// Callback to watch allowed.json and recache
//------------------------------------------------------------------------------
function scanConfigurationsForChanges() {
    global $configs;
    foreach (Humble::getEntity('humble/modules')->setEnabled('Y')->fetch() as $module) {
        print_r($module);
        die();$systemfiles['../application.xml']
        
    }
}

//------------------------------------------------------------------------------
function scanFilesForChanges() {
    global $files;
}

//------------------------------------------------------------------------------
function triggerWorkflows() {
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
        $is_production  = \Environment::isProduction();                         //must do in loop since someone can change this in the admin panel at any time
        $duration       = time();
        $now            = time() - $offset_time - $started;
        foreach ($cadence['handlers'] as $component => $handler) {
            $t = $handler['multiple'] * $cadence['period'];
            if (($now % $t) == 0) {
                $start  = time();
                logMessage("Processing ".ucfirst($component)." Now...");
                foreach ($handler['callbacks'] as $callback) {
                    $callback();
                }
                logMessage(ucfirst($component)." Processing took ".($start - time())." seconds");                
            }
        }
        $offset_time += (time() - $duration);                                   
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


