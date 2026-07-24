<?php
/*
    ____  __      __               _____      __            
   / __ \/ /___ _/ /____  _____   / ___/___  / /___  ______ 
  / /_/ / / __ `/ __/ _ \/ ___/   \__ \/ _ \/ __/ / / / __ \
 / ____/ / /_/ / /_/  __(__  )   ___/ /  __/ /_/ /_/ / /_/ /
/_/   /_/\__,_/\__/\___/____/   /____/\___/\__/\__,_/ .___/ 
                                                   /_/         
 
     1) Allocate required directories
     2) Set Default Options Array
     3) If module has overrides for options array, load and set them
     4) Basic Plates rendering engine allocations
     5) If the module has a plugins file, load that here

 */
    $Plates = false;

    if (is_dir($template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater)) {

        if (is_dir($optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Plates')) {
            if (file_exists($optdir.'/Config.php')) {
                require_once($optdir.'/Config.php');
            }
        }
        
        //Renderer here
        
        if (is_dir($optdir)) {    
           if (file_exists($optdir.'/Plugins.php')) {
                require_once($optdir.'/Plugins.php');
            } 
        }
    }
?>
