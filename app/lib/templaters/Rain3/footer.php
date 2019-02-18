<?php
function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $rain;

	//***************************************************************************************
	//Look to see if that action has a "view" template (MVC), if so, throws the model at it *
	//***************************************************************************************
	//
    $config = array(
        'tpl_dir'=> 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$controller.'/'.$templater.'/',
        'tpl_ext'=> 'rain',
        'cache_dir'=> 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views_cache'])
    );
    Tpl::configure($config);
    $template = $config['tpl_dir'].'/'.$tpl.'.rain';
    if (file_exists($template))
    {
        if (count($models) > 0) {
            foreach($models as $handle => $modelVar) {
                $rain->assign($handle,$modelVar);
            }
        }
        $rain->draw($tpl);
    }
}
if (!$abort) {
    manageView($controller,$templater,($view!==false) ? $view : $method);
}
$view = false;
foreach ($chainActions as $idx => $action) {
    $view = false;
    //$models = array();  //maybe?
    if (!$abort) {
        processMethod($action);
    }
    if (!$abort) {
        manageView($chainControllers[$idx],$templater,($view!==false) ? $view : $action);
    }
}
?>