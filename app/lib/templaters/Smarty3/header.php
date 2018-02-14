<?php
require_once('vendor/smarty/smarty/libs/Smarty.class.php');
$smarty 	= new Smarty();

$original_template_directory = $smarty->template_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$controller.'/'.$templater;
$smarty->compile_dir  = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']);
$smarty->config_dir   = 'lib/templaters/Smarty3/config';
$smarty->cache_dir    = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']);
if (!is_dir($smarty->cache_dir)) {
    @mkdir($smarty->cache_dir);
}
?>