<?php

function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $core;
    global $latte;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
    //***************************************************************************************
    //
    $template = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.latte';
    $core_tpl = 'Code/'.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.latte';
    if (file_exists($template) || file_exists($core_tpl)) {
        $cache      =  'Code/'.$module['package'].'/'.str_replace('_','/',$module["views_cache"]);
        $t_plate   = file_exists($template) ? 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater : 'Code/'.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater;

        $latte->setTempDirectory($cache);
        $latte->render($t_plate.'/'.$tpl.'.latte', $models);
        // or render to variable (maybe replace Rain with Latte?
        //$output = $latte->renderToString('template.latte', $params);        
        
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
            manageView($chainControllers[$idx],$templater,($view!==false) ? $view : $action);
        }
    }
}
?>

