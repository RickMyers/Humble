<?php
$permissions = $permissions->load();
if ($permissions['admin'] == 'Y') {
    $packages   = \Humble::getPackages();
    $utility    = \Environment::getRefresher();
    if ($util->getNamespace()) {
        $module = \Humble::getModule($util->getNamespace(),true);
        $package = ($module['package']) ? $module['package'] : $util->getPackage();
        $etc = ''.$module['package'].'/'.$util->getNamespace().'/etc/config.xml';
        print("Refreshing: ".$etc."\n");
        $utility->refresh($etc);        
    } else {    
        foreach ($packages as $idx => $package) {
            $modules = \Humble::getModules($package);
            foreach ($modules as $iidx => $module) {
                $etc = ''.$package.'/'.$module.'/etc/config.xml';
                if (file_exists($etc)) {
                    print("Processing: ".$etc."\n");
                    $utility->refresh($etc);
                }
            }
        }
    }
} else {
    print("Not Authorized");
}

?>