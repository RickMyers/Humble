<?php

$s = microtime(true);
require "Humble.php";
/*require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";*/
try {
    //\Application::cache(false);
    $mdb = \Humble::collection('paradigm/events');
    foreach ($mdb->setName('userLogin')->rows(10)->fetch() as $id => $doc) {
       print_r($doc);
    }
    
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");


