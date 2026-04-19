<?php
/* _____                      __           ______
  / ___/____ ___  ____ ______/ /___  __   / ____/
  \__ \/ __ `__ \/ __ `/ ___/ __/ / / /  /___ \  
 ___/ / / / / / / /_/ / /  / /_/ /_/ /  ____/ /  
/____/_/_/_/ /_/\__,_/_/   \__/\__, /  /_____/   
      / __ \/ /_  ______ _(_)_/____/___          
     / /_/ / / / / / __ `/ / __ \/ ___/          
    / ____/ / /_/ / /_/ / / / / (__  )           
   /_/   /_/\__,_/\__, /_/_/ /_/____/            
                 /____/     
  
   Define/Register custom plugins here
 
 */

/*
  function myPlugin($stuff=null) {
     $newStuff = strrev($stuff);
     return $newStuff;
  }
  $smarty->registerPlugin("modifier","reverse","myPlugin");
 */

$smarty->registerPlugin("modifier","ucfirst", "ucfirst");
$smarty->registerPlugin("modifier","json_encode", "json_encode");
$smarty->registerPlugin("modifier","json_decode", "json_decode");
