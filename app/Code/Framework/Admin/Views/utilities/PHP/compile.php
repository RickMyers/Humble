<?php
/**
 * This is an example of using PHP as a view.  It does have some vulnerabilities 
 * so is not recommended... but yes you can do this.
 */
if ($_SESSION['admin_id'] ?? false) {
    if ($namespace = $util->getNamespace()) {
        $module     = \Humble::module($util->getNamespace());
        $controller = $module['controllers'];
        $files      = $util->listDirectory('Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']),true);
        $compiler   = \Environment::getCompiler();
        foreach ($files as $file) {
            if (file_exists($file) && (strpos($file,'.xml')!==false)) {
                print("Compiling: ".$file."\n");
                $paths  = explode('/',$file);
                $file   = str_replace('.xml','',array_pop($paths));
                $compiler->compile($namespace.'/'.$file);
            }
        }
    } else {
        //work this later, all modules in a package compiled
            foreach (\Humble::entities('humble/modules')->setEnabled('Y')->fetch() as $iidx => $module) {
                $files      = $util->listDirectory('Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']),true);
                foreach ($files as $file) {
                    if (file_exists($file) && (strpos($file,'.xml')!==false)) {
                        print("Compiling: ".$file."\n");
                        $utility->compile($file);
                    }
                }
            }
    }
} else {
    print("Not Authorized\n");
}

