<?php
require_once 'CLI/CLI.php';
class Workflow extends CLI 
{
    
    /**
     * Generates all workflows contained within a module identified by a namespace
     * 
     * @param type $args
     */
    public static function generate() {
        print('Beginning generate'."\n");
        foreach (self::namespaces(self::arguments()) as $namespace) {
            $updater = \Environment::getUpdater();
            $updater->generateWorkflows($namespace);
        }

    }
    
    /**
     * 
     */
    public static function scan() {
        print('Beginning scan for workflow components'."\n");
        $installer = \Environment::getInstaller();
        foreach (self::namespaces(self::arguments()) as $namespace) {
            $installer->registerWorkflowComponents($namespace);
        }
    }
 
}
