<?php
/*
        ______         _          _____      __            
       /_  __/      __(_)___ _   / ___/___  / /___  ______ 
        / / | | /| / / / __ `/   \__ \/ _ \/ __/ / / / __ \
       / /  | |/ |/ / / /_/ /   ___/ /  __/ /_/ /_/ / /_/ /
      /_/   |__/|__/_/\__, /   /____/\___/\__/\__,_/ .___/ 
                     /____/                       /_/      
 
     1) Allocate required directories
     2) Set Default Options Array
     3) If module has overrides for options array, load and set them
     4) Basic Twig rendering engine allocations
     5) If the module has a plugins file, load that here

 */
    $twig               = false;
    if (!is_dir($cache  =  'Code/'.$module['package'].'/'.str_replace('_','/',$module["views_cache"]))) {
        @mkdir($cache);
    }
    if (is_dir($template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater)) {
        $default_options   = [
            'cache' => $cache,
            'auto_reload'=>true
        ];
        if (is_dir($optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Twig')) {
            if (file_exists($optdir.'/Config.php')) {
                require_once($optdir.'/Config.php');
            }
        }
        $twig       = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($template_dir), [
            array_merge($default_options, (!empty($options)) ? $options :[])
        ]);
        if (is_dir($optdir)) {    
           if (file_exists($optdir.'/Plugins.php')) {
                require_once($optdir.'/Plugins.php');
            } 
        }
    }
?>