<?php
/**
        __        _               
        \ \  ___ | |__            
         \ \/ _ \| '_ \           
      /\_/ / (_) | |_) |          
      \___/ \___/|_.__/           
 __                   _           
/ _\_ __   ___   ___ | | ___ _ __ 
\ \| '_ \ / _ \ / _ \| |/ _ \ '__|
_\ \ |_) | (_) | (_) | |  __/ |   
\__/ .__/ \___/ \___/|_|\___|_|   
   |_|                           
 */

require "Humble.php";
require "Constants.php";
ob_start();
$spooler = Humble::model('paradigm/system');
if ($spooler->runScheduler()) {
    $spooler->runLauncher();
}

//END