<?php
/*
    ____                          _____      __            
   / __ \_      ______  ____     / ___/___  / /___  ______ 
  / / / / | /| / / __ \/ __ \    \__ \/ _ \/ __/ / / / __ \
 / /_/ /| |/ |/ / /_/ / /_/ /   ___/ /  __/ /_/ /_/ / /_/ /
/_____/ |__/|__/\____/\____/   /____/\___/\__/\__,_/ .___/ 
                                                  /_/      
     1) Allocate required directories
     2) Set Default Options Array
     3) If module has overrides for options array, load and set them
     4) Basic Dwoo rendering engine allocations
     5) If the module has a plugins file, load that here

 */
    $Dwoo = false;

    if (is_dir($template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater)) {

        if (is_dir($optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Dwoo')) {
            if (file_exists($optdir.'/Config.php')) {
                require_once($optdir.'/Config.php');
            }
        }

        $Dwoo = new Dwoo\Core();
        
        if (is_dir($optdir)) {    
           if (file_exists($optdir.'/Plugins.php')) {
                require_once($optdir.'/Plugins.php');
            } 
        }
    }
?>

