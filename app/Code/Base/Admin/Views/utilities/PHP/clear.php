<?php
/**
 * This is an example of using PHP as a view.  It does have some vulnerabilities 
 * so is not recommended... but yes you can do this.
 */
if ($_SESSION['admin_id'] ?? false) {
    foreach ((($util->getNamespace() == "") ? Humble::entity('humble/modules')->setEnabled('Y')->fetch() : Humble::entity('humble/modules')->setNamespace($util->getNamespace())->fetch()) as $module) {
        if ($util->purgeDirectory('Code/'.str_replace('_','/',$module['package'].'/'.$module['controller_cache']))) {
            print("SUCCESS: The ".$module['namespace']." controller cache was cleared\n");
        } else {
            print("ERROR: The ".$module['namespace']." controller cache was not cleared due to an unknown error\n");
        }
        if ($util->purgeDirectory('Code/'.str_replace('_','/',$module['package'].'/'.$module['views_cache']))) {
            print("SUCCESS: The ".$module['namespace']." views cache was cleared\n");
        } else {
            print("ERROR: The ".$module['namespace']." views cache was not cleared due to an unknown error\n");
        }
    }
} else {
    print("You are not authorized to perform that action\n");
}
?>
