<?php
/**
    _|      _|  _|                                  _|_|_|                                  _|
    _|_|  _|_|        _|_|_|  _|  _|_|    _|_|    _|          _|_|    _|  _|_|  _|      _|        _|_|_|    _|_|
    _|  _|  _|  _|  _|        _|_|      _|    _|    _|_|    _|_|_|_|  _|_|      _|      _|  _|  _|        _|_|_|_|
    _|      _|  _|  _|        _|        _|    _|        _|  _|        _|          _|  _|    _|  _|        _|
    _|      _|  _|    _|_|_|  _|          _|_|    _|_|_|      _|_|_|  _|            _|      _|    _|_|_|    _|_|_|

      _|_|    _|_|_|    _|_|_|
    _|    _|  _|    _|    _|
    _|_|_|_|  _|_|_|      _|
    _|    _|  _|          _|
    _|    _|  _|        _|_|_|

    MicroService Router

    o A request of the form /namespace/controller/action has been intercepted
    o We will break up the URI looking for the namespace
    o We use the namespace to XREF the server that hosts this URI resources
    o We forward the request there
    o Ideally we will rely on a cache mechanism of some kind to store the xref information
 */

$secure     = (isset($_SERVER['HTTPS']) && (strtoupper($_SERVER['HTTPS'])=='ON'));
$protocol   = $secure ? 'ssl' : 'http';
$URL        = $secure ? 'https://'.$namespace.$_SERVER['HTTP_HOST'] : 'http://'.$namespace.$_SERVER['HTTP_HOST'];
$HURL       = $URL.'/'.$namespace.'/'.$controller.'/'.$method;
//file_put_contents('headers.txt',print_r($headers,true));
//die();
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
print(file_get_contents($HURL,false,stream_context_create($headers)));
