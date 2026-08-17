<?php
/*
    ____                 _____      __            
   / __ \__  ______ _   / ___/___  / /___  ______ 
  / /_/ / / / / __ `/   \__ \/ _ \/ __/ / / / __ \
 / ____/ /_/ / /_/ /   ___/ /  __/ /_/ /_/ / /_/ /
/_/    \__,_/\__, /   /____/\___/\__/\__,_/ .___/ 
            /____/                       /_/         
 
     1) Allocate required directories
     2) Set Default Options Array
     3) If module has overrides for options array, load and set them
     4) Basic Pug rendering engine allocations
     5) If the module has a plugins file, load that here

 */
    $Pug = false;
    $Options = [];
    if (is_dir($template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater)) {

        if (is_dir($optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Pug')) {
            if (file_exists($optdir.'/Config.php')) {
                require_once($optdir.'/Config.php');
            }
           if (file_exists($optdir.'/Plugins.php')) {
                require_once($optdir.'/Plugins.php');
            }             
        }
        
        $Options = [
          'paths' => [
            $template_dir,
          ],
          'cache_dir' => 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/Cache'            
        ];        
        $Pug = new Phug\Renderer($Options);
        
    }
?>
