<?php
$mustache 	= new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader('Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$controller.'/'.$templater),
    'cache' => 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']),
    'cache_file_mode' => 0666
]);
?>