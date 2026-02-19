<?php
/**
                   _______ __               
                  / ____(_) /__             
                 / /_  / / / _ \            
    __  ___     / __/ / / /  __/            
   /  |/  /___ /_/__ /_/_/\___/_ ____  _____
  / /|_/ / __ `/ __ \/ __ `/ __ `/ _ \/ ___/
 / /  / / /_/ / / / / /_/ / /_/ /  __/ /    
/_/  /_/\__,_/_/ /_/\__,_/\__, /\___/_/     
                         /____/             
  Just drives the periodic check for files
          arriving in folders
 */
require "Humble.php";
require "Constants.php";
Main:
    ob_start();
    $spooler = Humble::model('paradigm/system');
    $spooler->manageFiles();

//END
