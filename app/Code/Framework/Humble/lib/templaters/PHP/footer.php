<?php
    function manageView($controller,$templater,$tpl) {
        global $module;
        global $models;

        //***************************************************************************************
        //Look to see if that action has a "view" template (MVC), if so, throws the model at it *
        //***************************************************************************************
        //
        $template = 'Code/'.$module['package'].'/'.str_replace('_','/',$module["views"]).'/'.$controller.'/'.$templater.'/'.$tpl.'.php';
        if (file_exists($template)) {
            foreach ($models as $model => $obj) {
                $$model = $obj;
            }
            require_once($template);
        } else {
            print('not found');
        }
    }
    if (!$abort) {
        manageView($controller,$templater,(($view!==false) ? $view : $method));
    }
    $view = false;
    foreach ($chainActions as $idx => $action) {
        $view = false;
        if (!$abort) {
            processMethod($action);
        }
        if (!$abort) {
            manageView($chainControllers[$idx],$templater,(($view!==false) ? $view : $method));
        }
    }    
?>
