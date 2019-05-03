<?php

function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $core;

	//***************************************************************************************
	//Look to see if that action has a "view" template (MVC), if so, throws the model at it *
	//***************************************************************************************
	//
    $template = ''.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.twig';
    $core_tpl = ''.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.twig';
    if (file_exists($template) || file_exists($core_tpl)) {
        $cache      =  ''.$module['package'].'/'.str_replace('_','/',$module["views_cache"]);
        $t_plate   = file_exists($template) ? ''.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater : ''.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater;
        $loader     = new Twig_Loader_Filesystem($t_plate);
        $twig       = new Twig_Environment($loader,array('cache'=> $cache, 'auto_reload'=>true));
        $template   = $twig->loadTemplate($tpl.".twig");
        $template->display($models);
        unset($loader);
    } else {
        \Log::console('A view for action '.$tpl.' was not found');
    }
}
if (!$abort) {
    if ($views) {
        foreach ($views as $v) {
            manageView($controller,$templater,$v);
        }
    } else {
        manageView($controller,$templater,($view!==false) ? $view : $method);
    }
}
foreach ($chainActions as $idx => $action) {
    $view   = false;
    $views  = [];
    //$models = [];  //maybe? otherwise we just keep adding to the models that are passed into each chained action and their views
    if (!$abort) {
        processMethod($action);
    }
    if (!$abort) {
        if ($views) {
            foreach ($views as $v) {
                manageView($controller,$templater,$v);
            }
        } else {        
            manageView($chainControllers[$idx],$templater,($view!==false) ? $view : $action);
        }
    }
}
?>