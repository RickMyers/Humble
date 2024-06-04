<?php

$xml = simplexml_load_file('app/etc/application.xml');
print($xml->version->framework);

