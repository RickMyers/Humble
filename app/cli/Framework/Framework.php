<?php
require_once 'cli/CLI.php';
class Framework extends CLI 
{

    /**
     * Checks to see if a namespace is already being used
     */
    public static function namespaceAvailability() {
        $args = self::arguments();
        if ($data = \Humble::entity('humble/modules')->setNamespace($args['namespace'])->load(true)) {
            
            print("That namespace is already in use\n\n");
            print("Information on that module follows:\n");
            self::printModule($data);
        } else {
            print("\nThat namespace ({$args['namespace']}) is available\n\n");
        }

    }
    
    /**
     * Checks to see if a database prefix is already being used
     */
    public static function prefixAvailability() {
        $args = self::arguments();
        if ($data = \Humble::entity('humble/modules')->setPrefix($args['prefix'])->load(true)) {                
            print("That prefix is already in use\n\n");
            print("Information on that module follows:\n");
            self::printModule($data);
        } else {
            print("\nThat ORM prefix ({$args['prefix']}) is available\n\n");
        }
    }

}



