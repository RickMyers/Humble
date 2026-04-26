<?php
require_once('vendor/smarty/smarty/libs/Smarty.class.php');
use Smarty\Smarty;
$smarty = new Smarty();
$optdir = 'Code/'.$module['package'].'/'.$module['module'].'/lib/Templaters/Smarty';

$smarty->setTemplateDir($original_template_directory = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$controller.'/'.$templater);
$smarty->setCompileDir('Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']));
$smarty->setConfigDir('lib/templaters/Smarty/config');
$smarty->setCacheDir($cache_dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache']));

if (!is_dir($cache_dir)) {
    @mkdir($cache_dir);
}

if (is_dir($optdir)) {
    if (file_exists($optdir.'/Config.php')) {
        require_once($optdir.'/Config.php');
    }
    if (file_exists($optdir.'/Plugins.php')) {
        require_once($optdir.'/Plugins.php');
    } 
}
?>