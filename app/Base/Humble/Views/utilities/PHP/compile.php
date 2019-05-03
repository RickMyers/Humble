<?php
$permissions = $permissions->load();
if ($permissions['admin'] == 'Y') {
    if ($util->getNamespace()) {
        $module     = \Humble::getModule($util->getNamespace());
        $controller = $module['controller'];
        $files      = $util->listDirectory(''.$module['package'].'/'.str_replace('_','/',$module['controller']),true);
        foreach ($files as $file) {
            if (file_exists($file) && (strpos($file,'.xml')!==false)) {
                print("Compiling: ".$file."\n");
                $compiler = \Environment::getCompiler();
                $compiler->setFile($file);
                $compiler->setInfo($module);
                $compiler->setNamespace($module['namespace']);
                $compiler->setPackage($module['package']);
                $compiler->setDestination($module['package'].'/'.str_replace('_','/',$module['controller_cache']));
                $compiler->compile();
            }
        }
    } else {
        //work this later, all modules in a package compiled
        $packages   = \Humble::getPackages();
        foreach ($packages as $idx => $package) {
            $modules = \Humble::getModules($package);
            foreach ($modules as $iidx => $config) {
                $module     = \Humble::getModule($config);
                $controller = $module['controller'];
                $files      = $util->listDirectory(''.$package.'/'.str_replace('_','/',$controller),true);
                foreach ($files as $file) {
                    if (file_exists($file) && (strpos($file,'.xml')!==false)) {
                        print("Compiling: ".$file."\n");
                        //$utility->compile($file);
                    }
                }
            }
        }
    }
} else {
    print("Not Authorized");
}

?>