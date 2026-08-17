<?php
//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function manageView($controller,$templater,$tpl) {
    global $models;
    global $Savant;
    global $module;

    //***************************************************************************************
    //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
    //***************************************************************************************
    $templateDir = 'Code/'.$module['package'].'/'.$module['module'].'/Views/'.$controller.'/'.$templater;
    $template    = $templateDir.'/'.$tpl.'.tpl.php' ;
    if (file_exists($template))  {
        $Savant->addPath('template',$templateDir);
        foreach ($models as $handle => $modelVar) {
            $Savant->assign($handle,$modelVar);
        }
        $Savant->display($tpl.'.tpl.php');
    }
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
            if (!$skipView) {
                manageView($chainControllers[$idx],$templater,($view!==false) ? $view : $action);
            }
        }
    }
}
?>