<?php
/*
           ______          __            
          / ____/___  ____/ /__          
         / /   / __ \/ __  / _ \         
   _____/ /___/ /_/ / /_/ /  __/         
  / ___/\____/\____/\__,_/\___/___  _____
  \__ \/ ___/ __ `/ __ \/ __ \/ _ \/ ___/
 ___/ / /__/ /_/ / / / / / / /  __/ /    
/____/\___/\__,_/_/ /_/_/ /_/\___/_/     
                                         
Scans for changes using Reflection...
  (and that's the problem)
*/
main:

require_once 'Humble.php';
print('Scanning External Directories for Workflow Components'."\n");
$updater    = \Environment::getUpdater();
$cadence    = Humble::cache('cadence-config');
$external   = \Environment::application('external');
$externals  = Humble::cache('externals-array');
foreach ($external->directory as $idx => $directory) {
    if (($dh = dir($directory)) !== false) {
        while ($entry = $dh->read()) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (strpos($entry,'.php')) {
                $file = $directory.'/'.$entry;
                if (!isset($externals[$file]) || ($externals[$file] !== ($time = filemtime($file)))) {
                    //new or changed, go ahead and scan it
                    print('External change detected, scanning file: '.$file."\n");
                    $updater->scanAndRegisterExternalComponents($directory,$entry);
                    $externals[$file] = filemtime($file);
                }
                clearstatcache(true,$file);
            }
        }
    }
}