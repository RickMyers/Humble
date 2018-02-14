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

    $mongo = isset($_POST['mongo'])   ? $_POST['mongo']    : false;
    if ($mongo) {
        chdir('app');
        require 'vendor/autoload.php'; // include Composer's autoloader

        $client = new MongoDB\Client("mongodb://".$mongo);

        try {
            $dbs = $client->listDatabases();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)   {
            die('FAILED TESTING MONGO CONNECTION!');
        }
        die('SUCCESSFLY CONNECTED TO MONGODB');
    } else {
        die('ERROR: The required fields were not passed');
    }
?>
