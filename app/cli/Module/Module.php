<?php
require 'cli/CLI.php';
class Module extends CLI 
{
    public static function update() {
        print('did it!');
    }
    
    public static function updateModule() {
        $args      = self::arguments();
        print_r($args);
        die();
        $namespace = $args['namespace'];
        $workflows = $args['workflow'] ?? false;
        if ($namespace) {
            $modules = ($namespace==='*') ? \Humble::getEntity('humble/modules')->setEnabled('Y')->fetch() : explode(',',$namespace);
            print("\n\nThe following modules will be updated:\n\n"); $ctr=0;
            foreach ($modules as $module) {
                print("\t".++$ctr.') '.(is_array($module) ? $module['namespace'] : $module)."\n");
            }
            print("\n");
            $updater = \Environment::getUpdater();            
            foreach ($modules as $module) {
                $namespace = (is_array($module) ? $module['namespace'] : $module);
                $updater->output('BEGIN','');
                $updater->output('BEGIN',"=== Beginning update of Namespace: ".$namespace." ===");
                updateIndividualModule($updater->reset(),$namespace);
                //if (strtoupper($workflows)==='Y') {
                    $updater->generateWorkflows($namespace);
                //}
                //print(ob_get_clean());
            }
        } else {
            print('I need the namespace of the module to update passed in [namespace=ns]');
        }
    }
}



