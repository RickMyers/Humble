<?php
/**
               __        _                   
               \ \  ___ | |__                
                \ \/ _ \| '_ \               
             /\_/ / (_) | |_) |              
             \___/ \___/|_.__/               
   __                        _               
  / /  __ _ _   _ _ __   ___| |__   ___ _ __ 
 / /  / _` | | | | '_ \ / __| '_ \ / _ \ '__|
/ /__| (_| | |_| | | | | (__| | | |  __/ |   
\____/\__,_|\__,_|_| |_|\___|_| |_|\___|_|   
 
 */

require "Humble.php";
require "Constants.php";

$spooler = Humble::getModel('paradigm/system');
$spooler->runLauncher();

//END
