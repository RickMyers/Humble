<?php
/*
   _____ ___              _____      __            
  / ___// (_)___ ___     / ___/___  / /___  ______ 
  \__ \/ / / __ `__ \    \__ \/ _ \/ __/ / / / __ \
 ___/ / / / / / / / /   ___/ /  __/ /_/ /_/ / /_/ /
/____/_/_/_/ /_/ /_/   /____/\___/\__/\__,_/ .___/ 
                                          /_/      
 
     1) Allocate required directories
     2) Set Default Options Array
     3) If module has overrides for options array, load and set them
     4) Basic Slim rendering engine allocations
     5) If the module has a plugins file, load that here

 */
    $Slim = false;

    if (is_dir($template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater)) {

        if (is_dir($optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Slim')) {
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
