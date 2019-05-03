<?php
    $permissions = $permissions->load();
    if (isset($permissions['super_user']) && $permissions['super_user']==='Y') {
        $modules = [];
        if ($util->getNamespace() == "") {
            foreach (\Humble::getPackages() as $package) {
                foreach (\Humble::getModules($package) as $namespace) {
                    $modules[] = \Humble::getModule($namespace);
                }
            }
        } else {
            $modules[] = \Humble::getModule($util->getNamespace());
        }
        foreach ($modules as $module) {
            if ($util->purgeDirectory(''.str_replace('_','/',$module['package'].'/'.$module['controller_cache']))) {
                print("SUCCESS: The ".$module['namespace']." controller cache was cleared\n");
            } else {
                print("ERROR: The ".$module['namespace']." controller cache was not cleared due to an unknown error\n");
            }
            if ($util->purgeDirectory(''.str_replace('_','/',$module['package'].'/'.$module['views_cache']))) {
                print("SUCCESS: The ".$module['namespace']." views cache was cleared\n");
            } else {
                print("ERROR: The ".$module['namespace']." views cache was not cleared due to an unknown error\n");
            }
        }
    } else {
        print("You are not authorized to perform that action");
    }
?>
