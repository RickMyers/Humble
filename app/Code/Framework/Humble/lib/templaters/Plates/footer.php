<?php
function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $Plates;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
    //***************************************************************************************
    //
    
    $template = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.tpl';
    if (file_exists($template)) { 
        echo $Plates->render($tpl.".twig", $models);
    }
}
//*******************************************************************************************
//If critical error occurred during controller processing, prevent a view from being executed
//*******************************************************************************************
if (!$abort) {
    if ($views) {
        foreach ($views as $v) {
            manageView($controller,$templater,$v);
        }
    } else {
        manageView($controller,$templater,($view!==false) ? $view : $method);
    }
}
//*******************************************************************************************
//Now let us process any "chain" actions... 
//*******************************************************************************************
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
