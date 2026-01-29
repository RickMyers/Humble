<?php

require "Humble.php";

$faker = Humble::fake();

$user = Humble::entity('users');
$password = '';         //set to whatever you want prior to running this

for ($i=0; $i<50; $i++) {
    $f_name = $faker->firstName();
    $l_name = $faker->lastName();
    $u_name = $faker->userName();
    $email  = $faker->email(true);
    $gender = $faker->gender();
    $dob    = $faker->date('01/01/1940','12/31/2016');
    print($f_name.' '.$l_name.'-'.$u_name.'/'.$email.'---'.$gender.'---'.$dob."\n");
    $user->reset()->setFirstName($f_name)->setLastName($l_name)->setUserName($u_name)->setEmail($email)->setGender($gender)->setDateOfBirth($dob)->setPassword($password)->newUser();
}
 
