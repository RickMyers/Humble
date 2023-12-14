<?php
/**
 * This is an example of using PHP as a view.  It does have some vulnerabilities 
 * so is not recommended... but yes you can do this.
 */
if ($_SESSION['admin_id'] ?? false) {
    $packages   = \Humble::packages();
    $utility    = \Environment::getInstaller();
    if ($util->getPackage()  && $util->getRoot() && $util->getNamespace()) {
        $etc = 'Code/'.$util->getPackage().'/'.$util->getRoot().'/etc/config.xml';
        print("Processing: (".$util->getNamespace().")".$etc."\n");
        $utility->install($etc);
    } else {
        foreach ($packages as $idx => $package) {
            $modules = \Humble::modules($package);
            foreach ($modules as $iidx => $module) {
                $etc = 'Code/'.$package.'/'.$module.'/etc/config.xml';
                if (file_exists($etc)) {
                    print("Processing: ".$etc."\n");
                    $utility->install($etc);
                }
            }
        }
    }
}
