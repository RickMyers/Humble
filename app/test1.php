<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";

try {
    if (Environment::isRunning('php','Cadence.php')) {
        print('Cadence is running'."\n");
    } else {
        print('Cadence is NOT running'."\n");
    }
    if (Environment::isRunning('node','main.js')) {
        print('HUB is running'."\n");
    } else {
        print('HUB is NOT running'."\n");
    }
    if (Environment::isRunning('php','Proxy.php')) {
        print('Proxy is running'."\n");
    } else {
        print('Proxy is NOT running'."\n");
    }    
    ///print_r($x = Humble::model('admin/services')->list());
    //Environment::stopCommandProxy();
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");
