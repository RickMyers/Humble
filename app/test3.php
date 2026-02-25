<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";

try {
    
    print("Attempting kill\n");
   print(Environment::killTask(1234)."\n");
 //   print(Environment::stopCommandProxy());
    print("Back...\n");
    
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");