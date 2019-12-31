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
print(file_get_contents($HURL,false,stream_context_create($headers)));
