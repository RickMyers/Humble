<?php
/**
 * This is an example of using PHP as a view.  It does have some vulnerabilities 
 * so is not recommended... but yes you can do this.
 */
if ($_SESSION['admin_id'] ?? false) {
    $packages   = \Humble::packages();
    $utility    = \Environment::getUpdater();
    if ($namespace  = $module->getNamespace()) {
        $module = \Humble::module($namespace,true);
        if ($module['configuration']) {
            $etc = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['configuration']).'/config.xml';
            if (file_exists($etc)) {
                $utility->registerEntities($namespace);
                $utility->update($etc);
            }
        } else {
            print("Missing location for configuration xml, check previous install, and correct source xml");
        }
    } else {
        print('processing block');
        foreach ($packages as $idx => $package) {
           $modules = \Humble::modules($package);
           foreach ($modules as $iidx => $module) {
               $etc = 'Code/'.$package.'/'.$module.'/etc/config.xml';
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

