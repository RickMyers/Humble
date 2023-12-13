<?php

$parts  = explode(DIRECTORY_SEPARATOR,getcwd());

$ns     = $parts[count($parts)-1];

function removeDanglingVolumes($namespace,$object=false) {
    $skip = true;
    exec('docker volume ls -f dangling=true',$results);
    foreach ($results as $volume) {
        if ($skip) {
            $skip = false;
            continue;
        }
        $parts = explode(' ',preg_replace('/\s+/', ' ', $volume));
        print(shell_exec('docker volume rm '.$parts[1])."\n");
    }
}
$pgm = array_shift($argv);
if ($cmd = array_shift($argv)) {
    switch (strtolower($cmd)) {
        case "clean"  :
            removeDanglingVolumes($ns,'volume');
            break;
        case "up" :
            print(shell_exec('docker compose up -d ')."\n");
            break;
        case "down" :
            print(shell_exec('docker compose down')."\n");
            break;
        case "build" :
            print(shell_exec('docker build -t '.$ns.' .')."\n");
            break;
        case "lsc":
        case "listc" :
            print(shell_exec('docker container ls')."\n");
            break;
        case "lsv":
        case "listv" :
            print(shell_exec('docker volume ls')."\n");
            break;
        case "lsi":
        case "listi" :
            print(shell_exec('docker image ls')."\n");
            break;
        case "deli" :
            if ($img = (array_shift($argv) ?? $ns)) {
                print(shell_exec('docker image rm '.$img)."\n");
            }
            break;   
        case "delv" :
            if ($vol = (array_shift($argv) ?? $ns)) {
                print(shell_exec('docker volume rm '.$vol)."\n");
            }
            break;  
        case "delc" :
            if ($con = (array_shift($argv) ?? $ns)) {
                print(shell_exec('docker container rm '.$con)."\n");
            }
            break;   
        case "remove" :
            if ($ns) {
                print(shell_exec('docker container stop '.$ns));
                print(shell_exec('docker container stop '.$ns.'_mysql'));
                print(shell_exec('docker container stop '.$ns.'_mongodb'));
                print(shell_exec('docker container rm '.$ns));
                print(shell_exec('docker container rm '.$ns.'_mysql'));
                print(shell_exec('docker container rm '.$ns.'_mongodb'));
                print(shell_exec('docker image rm '.$ns));
                print(shell_exec('docker image rm '.$ns.'_mysql'));
                print(shell_exec('docker image rm '.$ns.'_mongodb'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_mongodb_cfg'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_mongodb_data'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_mysql_cfg'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_settings'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_db_data'));
                print(shell_exec('docker volume rm '.$ns.'_'.$ns.'_web_cfg'));
            }
            break;
        default:
            print("I don't handle those... [".$cmd."]\n");
            break;
    }
    
}