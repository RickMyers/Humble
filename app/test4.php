<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";

try {
    $users = \Humble::entity('default/user/identification');
    $users->setId(1)->setFavoriteDog('poodle')->save();
    print_r($users->setId(1)->load());
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");
