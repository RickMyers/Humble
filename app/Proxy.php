<?php
require_once 'Humble.php';
require_once 'Environment.php';
/**
   ______                                          __
  / ____/___  ____ ___  ____ ___  ____ _____  ____/ /
 / /   / __ \/ __ `__ \/ __ `__ \/ __ `/ __ \/ __  / 
/ /___/ /_/ / / / / / / / / / / / /_/ / / / / /_/ /  
\____/\____/_/_/_/ /_/_/ /_/ /_/\__,_/_/ /_/\__,_/   
            / __ \_________  _  ____  __             
           / /_/ / ___/ __ \| |/_/ / / /             
          / ____/ /  / /_/ />  </ /_/ /              
         /_/   /_/   \____/_/|_|\__, /               
                               /____/                
 
 WARNING:  This application is meant to run in the background
           with ROOT level permissions, the recommended exec
           command is shown below: 

           CMD: sudo nohup php Proxy.php > /dev/null 2>&1 &

           This program supports only a few commands (like 'kill')
           and should never be modified or allowed to run arbitrary
           commands at the Command Line.

           Use of this program in the above way is not required but
           is recommended to get full framework functionality
 */
Main:
    $proxy = Environment::application('proxy');
    if (!$proxy->port) {
        die("\nCommand Proxy is not configured\n");
    }
    $port  = $proxy->port;
    $host  = $proxy->host;
    $util  = $proxy->util;
    
    print($host.', '.$port.', '.$util."\n");

    print("Creating...\n");
    // Fabricate a new socket
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);

    print("Binding...\n");
    // Deploy the socket to a specific address and port
    socket_bind($socket, $host, $port);

    print("Listening...\n");
    // Standby for incoming connections
    socket_listen($socket);

    print("Incoming Connection...\n");
    // Embrace an incoming connection
    $client = socket_accept($socket);

    print("Reading Data...\n");
    // Digest data from the connected client
    $data = socket_read($client, 1024);

    print("Writing Response...\n");
    // Respond to the client
    socket_write($client, 'Server: ' . $data);

    // Seal the connection
    socket_close($client);
    socket_close($socket);    