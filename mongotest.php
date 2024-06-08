<?php

    $project = json_decode(file_get_contents('Humble.project'));
    $data = (file_exists('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml')) ? file_get_contents('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml') : die("Missing application.xml file, Install is not possible at this time.");
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
    $action = isset($_POST['action']) ? $_POST['action']    : false;
    $mongo  = isset($_POST['mongo'])   ? $_POST['mongo']    : false;
    if ($action) {
        $process    = isset($_POST['processname'])  ? $_POST['processname'] : false;
        $port       = isset($_POST['port'])         ? $_POST['port']        : false;
        $location   = isset($_POST['location'])     ? $_POST['location']    : false;
        $datadir    = isset($_POST['datadir'])      ? $_POST['datadir']     : false;
        switch ($action) {
            case "new"      :
                $rc = @mkdir($datadir,0775,true);
                if ($rc) {
                    @mkdir($datadir.'/log',0775,true);
                    @mkdir($datadir.'/data',0775,true);
                    $message = [
                       'rc' => 0,
                       'txt' => 'Unable to create the directory: '.$datadir
                    ];
                    die(json_encode($message));
                }
                $cfg = <<<CONFIG
systemLog:
    destination: file
    path: {$datadir}\log\mongo.log
storage:
    dbPath: {$datadir}\data
net:
   bindIp: 127.0.0.1
   port: {$port}
CONFIG;
                file_put_contents($datadir.'/mongod.cfg',$cfg);
                $cmd = <<<CMD
sc.exe create {$process} binPath= "\"{$location}\" --service --config=\"{$datadir}\mongod.cfg\"" DisplayName= "{$process}" start= "auto"
CMD;
                $message = [
                    'rc'  => 1,
                    'txt' => 'To create the instance, you will need to copy and paste the line below into a windows command terminal that has administrator privileges',
                    'cmd' => $cmd
                ];
                die(json_encode($message));
                break;
            default         :
                break;
        }
    } else if ($mongo) {
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
