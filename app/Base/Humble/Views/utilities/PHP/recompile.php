<?php
$a = 1;
if ($permissions->getSuperUser()=='Y') {
    $util   = \Environment::getCompiler();
    $ns     = \Humble::getNamespaces('Base');
    foreach ($ns as $idx => $namespace) {
        $module = \Humble::getModule($namespace);
        $src = ''.$module['package'].'/'.str_replace('_','/',$module['controller']);
        $controllers = $util->listDirectory($src,false,'.xml');
        foreach ($controllers as $cdx => $controller) {
            $controller = str_replace('.xml','',$controller);
            $identifier = $namespace.'/'.$controller;
            \Log::console("Recompiling: ".$identifier);
            $util->setInfo($module);
            //$util->_namespace($namespace);
           // $util->_controller($controller);
            $util->setNamespace($namespace);
            $util->setController($controller);
            $util->setSource($module['package'].'/'.str_replace('_','/',$module['controller']));
            $util->setDestination($module['package'].'/'.str_replace('_','/',$module['controller_cache']));
            $util->compile($identifier,true);
        }
    }
} else {
    ?> <h1>You are not Authorized to perform global actions!</h1>  <?php
}
?>