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
function initializeSocket() {
    global $socket, $proxy;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_bind($socket, $proxy->host, $proxy->port);
}
/* ----------------------------------------------------------------------------- */
function finalizeSocket() {
    global $client, $socket;
    socket_close($client);
    socket_close($socket);      
}
/* ----------------------------------------------------------------------------- */
function killTask($data=[]) {
    $result = '';
    if ($pid = isset($data['PID']) ? $data['PID'] : false) {
       $result = shell_exec('kill '.$pid);
    }
    print('Attempting to kill process '.$pid.'. Result='.$result."\n");
    return $result;     
}
/* ----------------------------------------------------------------------------- */
function saveFile($data=[]) {
    $result = 'File Not Saved';
    $filename = isset($data['filename']) ? $data['filename'] : false;
    if ($filename && is_file($filename)) {
       $result = (file_put_contents($filename,$data['source'])) ? 'File Saved' : 'Error';
    }
    print('Attempting to save file '.$filename.'. Result='.$result."\n");
    return $result;   
}
/* ----------------------------------------------------------------------------- */
function restartService($data=[]) {
    
}
/* ----------------------------------------------------------------------------- */
function banHost($host=false) {
    $result = '';
    $host   = isset($data['host']) ? $data['host'] : false;
    $util   = isset($data['util']) ? $data['util'] : 'ufw';
    print('Banning '.$host.' using '.$util.".\n");
    if ($host && $util) {
        switch ($util) {
            case 'ufw'  :
                $result = shell_exec('ufw deny from '.$host.' to any');
                break;
            case 'iptables'     :
                $result = shell_exec('iptables -I INPUT -s '.$host.' -j DROP');
                shell_exec("service iptables save");
                break;
            case 'firewalld'     :
                $result = shell_exec("firewall-cmd --permanent --add-rich-rule=\"rule family='ipv4' source address=\''.$host.'\' reject\"");
                shell_exec("firewall-cmd --reload");
                break;
            default     :
                break;
        }
    }
    return $result;
}
/* ----------------------------------------------------------------------------- */
function endProxy($data=[]) {
    global $run;
    print('Quiescing Command Proxy...'."\n");
    return $run = false;
}
/* ----------------------------------------------------------------------------- */
function setupOperations() {
    return [
        'kill' => [
            'help'      => 'Terminate a process running in the background',
            'handler'   => 'killTask',
            'response'  => true,
            'arguments' => [
                'PID'   => 'Integer process ID'
            ]
        ],
        'save' => [
            'help'      => 'An elevated (root) level service to save data over an existing file.  Useful for saving files not on the web root',
            'handler'   => 'saveFile',
            'response'  => false,
            'arguments' => [
                'filename'  => 'Full path inluding name of file',
                'source'    => 'Data to overwrite the file'
            ]
        ],
        'ban' => [
            'help'      => 'Permanently ban a host (IP Address)',
            'handler'   => 'banHost',
            'response'  => true,
            'arguments' => [
                'host'  => 'IP Address to ban'
            ]
        ],
        'end' => [
            'help'      => 'Quiesces [Shutdowns] the Command Proxy',
            'handler'   => 'endProxy',
            'response'  => false,
            'arguments' => [
                
            ]
        ]
    ];
}
/* ----------------------------------------------------------------------------- */
function showHelp() {
    global $operations;
    foreach ($operations as $cmd => $operation) {
        
    }
}
/* ----------------------------------------------------------------------------- */
Main:
    $proxy  = Environment::application('proxy');
    if (!$proxy->port) {
        die("\nCommand Proxy is not configured\n");
    }
    $operations = setupOperations();
    if (count($argv)>1) {
        showHelp();
    }    
    Environment::storePID('proxy.pid');    
    $socket = null;
    $client = null;
    $run    = true;
    initializeSocket(); 
    while ($run) {
        socket_listen($socket);
        $client = socket_accept($socket);
        $data   = json_decode(socket_read($client, 1024),true);
        if (isset($data['command'])) {
            if (isset($data['token'])) {
                if ($data['token'] === \Environment::securityToken()) {
                    if (isset($operations[$data['command']])) {
                        $method = $operations[$data['command']]['handler'];
                        $result = $method($data);
                        if ($operations[$data['command']]['response']) {
                            socket_write($client,$result);
                            socket_close($client);                            
                        }
                    } else {
                        print($data['command']." is not supported\n");
                    }
                } else {
                    print("Invalid Security Token\n");
                }
            } else {
                print("Unsecured Operations Are Not Permitted\n");
            }
        }
    }
End:
    finalizeSocket();
    Environment::removePID('proxy.pid');
    