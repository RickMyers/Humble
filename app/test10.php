<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
print("\n------------------------------------------------------------------------------\n");
print("- CURRENT ENVIRONMENT: ".Environment::state()."\n");
print("------------------------------------------------------------------------------\n\n");

$prompt = 'What is a good name for a group of eight cats?';
//$prompt = 'What is the capitol of Ohio?';
try {
    $x = Humble::model('google/model');
    
    print($x->setAddress('12718 Ridge Road, West Springfield, PA, 16443')->geocodeLookup());
    //print($x->geocodeLookup('12718 Ridge Road, West Springfield, PA, 16443'));
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("\n\nDone: ".microtime(true)-$s."\n");


