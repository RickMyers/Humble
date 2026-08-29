<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
require "CLI/Component/Component.php";
require "CLI/Framework/Framework.php";
require "CLI/Module/Module.php";
require "CLI/System/System.php";
require "CLI/Workflow/Workflow.php";
print("\n------------------------------------------------------------------------------\n");
print("- CURRENT ENVIRONMENT: ".Environment::state()."\n");
print("------------------------------------------------------------------------------\n\n");


try {
    Component::run("componentConfigurationTemplate",['uri'=>'workflow/generator/splitter']);
;
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("\n\nDone: ".microtime(true)-$s."\n");



