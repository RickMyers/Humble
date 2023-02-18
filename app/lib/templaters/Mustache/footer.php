<?php

function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $mustache;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throw the models at it *
    //***************************************************************************************
    //
    $template = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.mustache';
    if (file_exists($template)) {
        //$template   = $mustache->loadTemplate($tpl.".mustache");
        $mustache->render($tpl,$models);
    } else {
      //  \Log::console('A view for action '.$tpl.' was not found');
    }
}
//------------------------------------------------------------------------------------------
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