<?php
$project = json_decode(file_get_contents('Humble.project'));
$data = (file_exists('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml')) ? file_get_contents('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml') : die("Missing application.xml file.");
$xml  = simplexml_load_string($data);


print($xml->version->framework);

