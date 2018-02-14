<?php
        /* Development
		$this->userid	 	= "&&USERID&&";
		$this->password		= "&&PASSWORD&&";
		$this->database		= "&&DATABASE&&";
        $this->dbhost       = "&&HOST&&";
        */
    $data = (file_exists('application.xml')) ? file_get_contents('application.xml') : die("Install is not possible at this time.");
    $xml  = simplexml_load_string($data);
    if (!empty($xml)) {
        if (isset($xml->status)) {
            if (isset($xml->status->installer) && ((int)$xml->status->installer)) {
                //nop; everything is good
            } else {
                die("Executing the installation script is currently disabled");
            }
        } else {
            die("The application is not correctly configured.  Correct the application configuration file and try again");
        }
    } else {
        die("There is an error in the application configuration file");
    }
    $host = isset($_POST['dbhost'])   ? $_POST['dbhost']    : false;
    $uid  = isset($_POST['userid'])   ? $_POST['userid']    : false;
    $pwd  = isset($_POST['password']) ? $_POST['password']  : false;
    $db   = isset($_POST['db'])       ? $_POST['db']        : false;
    if ($host && $uid && $db) {
        $conn = @new mysqli($host,$uid,$pwd,$db);
        if ($conn->connect_errno) {
            die('FAILED!');
        } else {
            die('SUCCESS');
        }
    } else {
        die('ERROR: The required fields were not passed');
    }
?>