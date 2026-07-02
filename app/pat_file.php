<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";

$faker      = Humble::fake();
$pats       = 10000;
$bulk       = 500;
$tot        = 0;
$ctr        = 0;
$user       = Humble::entity('users');
$patients   = [];
for ($i=0; $i<$pats; $i++) {
    $tot++;
    $patient = [
        "first_name" => $faker->firstName(),
        "last_name" => $faker->lastName(),
        "gender" => $faker->gender(),
        "email" => $faker->email(true),
        "date_of_birth" => $faker->date('01/01/1940','12/31/2016'),
        "address" => $faker->streetAddress(),
        "city"   => $faker->city(),
        "state" => $faker->state(),
        "zip_code" => $faker->zipCode(),
        "phone_number" => $faker->phoneNumber(true)
    ];
    $patients[] = json_encode($patient);
    if ($ctr++ >= $bulk) {
        file_put_contents('patients.dat',implode("\n",$patients),FILE_APPEND);
        $ctr        = 0;
        $patients   = [];
    }
}
print("Total Patients Created: ".$tot."\n");
print("Done: ".microtime(true)-$s."\n"); 

