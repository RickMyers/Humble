<?php
require_once('vendor/smarty/smarty/libs/Smarty.class.php');
use Smarty\Smarty;
$smarty = new Smarty();

$smarty->setTemplateDir($original_template_directory = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$controller.'/'.$templater);
$smarty->setCompileDir('Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']));
$smarty->setConfigDir('lib/templaters/Smarty/config');
$smarty->setCacheDir($cache_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']));

if (!is_dir($cache_dir)) {
    @mkdir($cache_dir);
}

if (is_dir('lib/Smarty')) {
    if (file_exists('lib/Smarty/Config.php')) {
        require_once('lib/Smarty/Config.php');
    }
    if (file_exists('lib/Smarty/Plugins.php')) {
        require_once('lib/Smarty/Plugins.php');
    } 
}
?>