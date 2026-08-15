<?php

class Socket {
    
    private $port   = null;
    private $host   = null;
    private $socket = null;
    private $client = null;
    
    public function __construct($host=null,$port=null) {
        if (($this->host = $host) && ($this->port = $port)) {
            $this->initialize();
        }
    }
    
    public function initialize() {
        if ($this->host && $this->port) {
            $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_bind($this->socket, $this->host, $this->port);
        }
    }
    
    public function listen() {
        $run    = true;
        $ctr    = 0;
        while ($run) {
            socket_listen($this->socket);
            $this->client  = socket_accept($this->socket);
            $linger        = [
                'l_linger' => 0,
                'l_onoff'  => 1
            ];
            socket_set_option($this->client, SOL_SOCKET, SO_LINGER, $linger);        
            $data          = socket_read($this->client, 1024);
            if (($data === false) || ($data === '')) {
                die('End of data encountered, terminating'."\n");
            }
            print(++$ctr.') '.$data."\n");
        }       
    }
    
    public function finalize() {
        socket_close($this->client);
        socket_close($this->socket);         
    }
}
