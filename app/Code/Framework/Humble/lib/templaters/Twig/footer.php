<?php

function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $core;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
    //***************************************************************************************
    //
    
    $template = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.twig';
    $core_tpl = 'Code/'.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.twig';
    if (file_exists($template) || file_exists($core_tpl)) {
        $cache      =  'Code/'.$module['package'].'/'.str_replace('_','/',$module["views_cache"]);
        $t_plate   = file_exists($template) ? 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater : 'Code/'.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater;
        $loader     = new \Twig\Loader\FilesystemLoader($t_plate);
        $twig       = new \Twig\Environment($loader, [
            'cache' => $cache,
            'auto_reload'=>true
        ]);

        echo $twig->render($tpl.".twig", $models);
        unset($loader);
        unset($twig);
    } else {
        // \Log::console('A view for action '.$tpl.' was not found');
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
            if (!$skipView) {
                manageView($chainControllers[$idx],$templater,($view!==false) ? $view : $action);
            }
        }
    }
}
?>