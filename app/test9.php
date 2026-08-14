<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
try {
    foreach (Humble::model('humble/generator')->testGen(Event::get('myTestGenEvent',['stooges'=>['Larry','Curly','Moe']])) as $new_event) {
        if ($new_event === null) {
            break;
        }
        print($new_event->instance()."\n");
    }
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");

