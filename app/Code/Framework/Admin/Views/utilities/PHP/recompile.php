<?php
/**
 * This is an example of using PHP as a view.  It does have some vulnerabilities 
 * so is not recommended... but yes you can do this.
 */
if ($_SESSION['admin_id'] ?? false) {
    $util   = \Environment::getCompiler();
    foreach (\Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $idx => $module) {
        $namespace   = $module['namespace'];
        $src         = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']);
        $controllers = $util->listDirectory($src,false,'.xml');
        foreach ($controllers as $cdx => $controller) {
            $controller = str_replace('.xml','',$controller);
            $identifier = $namespace.'/'.$controller;
            \Log::console("Recompiling: ".$identifier);
            $util->setInfo($module);
            $util->setNamespace($namespace);
            $util->setController($controller);
            $util->setSource($module['package'].'/'.str_replace('_','/',$module['controllers']));
            $util->setDestination($module['package'].'/'.str_replace('_','/',$module['controllers_cache']));
            $util->compile($identifier,true);
        }
    }
} else {
    print("You do not have administration authority\n\n");
}
