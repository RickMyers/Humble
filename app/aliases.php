<?php
/**
    ____              __           
   / __ \____  __  __/ /____       
  / /_/ / __ \/ / / / __/ _ \      
 / _, _/ /_/ / /_/ / /_/  __/      
/_/ |_|\____/\__,_/\__/\___/       
   /   |  / (_)___ _________  _____
  / /| | / / / __ `/ ___/ _ \/ ___/
 / ___ |/ / / /_/ (__  )  __(__  ) 
/_/  |_/_/_/\__,_/____/\___/____/  
                                   
Allows for shorter names for routes
 
 */
require "Humble.php";
require "Environment.php";

$project = Environment::project();
$parts   = explode('/',$project->landing_page);
$path    = '/'.$project->namespace.'/'.$parts[2].'/404';
$alias   = $_GET['alias'];
if (!$aliases = Humble::cache('humble_route_aliases')) {
    $aliases  = [];
    if (file_exists($alias_file = 'Code/'.$project->package.'/'.$project->module.'/etc/route_aliases.json')){
        Humble::cache('humble_route_aliases',$aliases = json_decode(file_get_contents($alias_file),true));
    }
}
$path    = isset($aliases['/'.$alias]) ? $aliases['/'.$alias] : (isset($aliases[$alias]) ? $aliases[$alias] : $path);

header('Location: '.$path);
die();
