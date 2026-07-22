<?php
/*
  ______          __            
 /_  __/__  _____/ /_           
  / / / _ \/ ___/ __/           
 / / /  __(__  ) /_             
/_/ _\___/____/\__/             
   / __ \_____(_)   _____  _____
  / / / / ___/ / | / / _ \/ ___/
 / /_/ / /  / /| |/ /  __/ /    
/_____/_/  /_/ |___/\___/_/     
                                
Just a program for you to use for some dirty command line testing

 */
$s = microtime(true);
require "&&FACTORY&&.php"; 
require "Environment.php";
require "Log.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/&&PACKAGE&&/&&MODULE&&/includes/Custom.php";
print("\n------------------------------------------------------------------------------\n");
print("- CURRENT ENVIRONMENT: ".Environment::state()."\n");
print("------------------------------------------------------------------------------\n\n");
try {
    //Do Stuff Here
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    
}
print("\n\nDone: ".microtime(true)-$s."\n");
