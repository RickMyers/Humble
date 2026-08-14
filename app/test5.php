<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
require "CLI/Component/Component.php";
print("\n------------------------------------------------------------------------------\n");
print("- CURRENT ENVIRONMENT: ".Environment::state()."\n");
print("------------------------------------------------------------------------------\n\n");

$prompt = 'What is a good name for a group of eight cats?';
//$prompt = 'What is the capitol of Ohio?';
try {
//    $sc = new Component();
    
//    $sc::check('admin','entity');
    $lp  = Humble::model('llama/something');
    //$r = $lp->modelTags();
    //print_r($r);
    //die();
    print('Prompt: '.$prompt."...\n\n");
    $result = $lp->setStream(false)->setPrompt($prompt)->askOllama();
    print_r($result);
    print($result['response'] ?? 'No Answer Returned');
    print("\n\n");
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("\n\nDone: ".microtime(true)-$s."\n");

