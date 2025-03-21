<?php
/**
    ____              __           
   / __ \____  __  __/ /____       
  / /_/ / __ \/ / / / __/ _ \      
 / _, _/ /_/ / /_/ / /_/  __/      
/_/ |_|\____/\__,_/\__/\___/       
   /   |  / (_)___ _________  _____
  / /| | / / / __ `/ ___/ _ \/ ___/
 / ___ |/ / / /_/ (__  )  __(__  ) 
/_/  |_/_/_/\__,_/____/\___/____/  
                                   
Allows for shorter names for routes
 
 */
require "Humble.php";
require "Environment.php";

$project    = Environment::project();
$parts      = explode('/',$project->landing_page);
$path       = '/'.$project->namespace.'/'.$parts[2].'/404';
$alias      = $_GET['alias'];
if (!$aliases = Humble::cache('humble_route_aliases')) {
    $aliases  = [];
    if (file_exists($alias_file = 'Code/'.$project->package.'/'.$project->module.'/etc/route_aliases.json')){
        Humble::cache('humble_route_aliases',$aliases = json_decode(file_get_contents($alias_file),true));
    }
}
$path      = isset($aliases['/'.$alias]) ? $aliases['/'.$alias] : (isset($aliases[$alias]) ? $aliases[$alias] : $path);
$HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1';
$protocol  = '';
if (substr($path,0,4)!=='http') {
    $isHttps = isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS'])==='ON' ? true :
               (isset($_SERVER['REQUEST_SCHEME']) && strtoupper($_SERVER['REQUEST_SCHEME']==='HTTPS') ? true : 
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])? true : false));
    if ($isHttps) {            
        $path      = 'https://'.$HTTP_HOST.$path;
        $protocol = 'ssl';
    } else {
        $path      = 'http://'.$HTTP_HOST.$path;
        $protocol = 'http';
    }
}
$opts         = [];
switch ($protocol) {
    case "ssl"  :   $opts['ssl'] = [
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                        "crypto_method" => STREAM_CRYPTO_METHOD_ANY_SERVER
                    ];
    case "http" :   $opts['http'] = [
                        'header' => "Content-Type: application/x-www-form-urlencoded",
                        "method" => "GET",
                        'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16", /*whats a little spoofage between friends? */
                        'Content-Length' => 0,
                        'content' => ''
                    ];
                    break;
    default     :
        break;
}
foreach (getallheaders() as $header => $value) {
    $opts[$protocol][$header] = $value;
}

if ($fp = fopen($path, 'rb', false, stream_context_create($opts))) {
    stream_set_timeout($fp,60000);
    $res = stream_get_contents($fp);            
    print($res);
};
die();
