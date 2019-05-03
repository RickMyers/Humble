<?php
$permissions = $permissions->load();
if ($permissions && ($permissions['admin'] == 'Y')) {
    $packages   = \Humble::getPackages();
    $utility    = \Environment::getUpdater();
    if ($module->getNamespace()) {
        $module = \Humble::getModule($module->getNamespace(),true);
        if ($module['configuration']) {
            $etc = ''.$module['package'].'/'.str_replace('_','/',$module['configuration']).'/config.xml';
            if (file_exists($etc)) {
                $utility->update($etc);
            }
        } else {
            print("Missing location for configuration xml, check previous install, and correct source xml");
        }
    } else {
        print('processing block');
        foreach ($packages as $idx => $package) {
           $modules = \Humble::getModules($package);
           foreach ($modules as $iidx => $module) {
               $etc = ''.$package.'/'.$module.'/etc/config.xml';
               if (file_exists($etc)) {
                   print("Processing: ".$etc."\n");
                   $utility->update($etc);
               }
            }
        }
    }
} else {
    print("Not Authorized");
}

?>