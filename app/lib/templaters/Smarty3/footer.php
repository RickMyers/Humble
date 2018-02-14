<?php
//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function manageView($controller,$templater,$tpl) {
    global $models;
    global $module;
    global $smarty;
    global $core;
    global $original_template_directory;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
    //***************************************************************************************
    $template = $smarty->template_dir[0].'/'.$tpl.'.tpl';
    $fe       = file_exists($template);
    if (!$fe) {
        $smarty->template_dir = 'Code/'.$core['package'].'/'.str_replace('_','/',$core["views"]).'/'.$controller.'/'.$templater;
        $template = $smarty->template_dir[0].'/'.$tpl.'.tpl';
    }
    if ($fe || file_exists($template))  {
        foreach ($models as $handle => $modelVar) {
            $smarty->assign($handle,$modelVar);
        }
        $smarty->display($tpl.'.tpl');
    }
    $smarty->template_dir = $original_template_directory;
}
//------------------------------------------------------------------------------
//If there are multiple requested views, process those, else look to see if 
// there's a defined view, else just use the method name as a view name, which
// is the default behavior
if (!$abort) {
    if ($views) {
        foreach ($views as $v) {
            manageView($controller,$templater,$v);
        }
    } else {
        manageView($controller,$templater,($view!==false) ? $view : $method);
    }
}
//------------------------------------------------------------------------------
//Now start rolling through the "chained" or sequential actions to follow next
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