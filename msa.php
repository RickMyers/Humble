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

    MicroService API Router

    o A request of the form /U/R/I has been intercepted
    o We will break up the URI looking for the namespace
    o We use the namespace to XREF the server that hosts this URI resources
    o We forward the request there
    o Ideally we will rely on a cache mechanism of some kind to store the xref information
 */

$uri = "URI";
print('Hello World: '.$uri);