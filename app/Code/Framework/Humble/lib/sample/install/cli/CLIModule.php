<?php
require_once 'CLI/CLI.php';
class &&MODULE&& extends CLI implements CLIInterface
{
    
    /**
     * Function is identified in the accompanying yaml file
     * 
     * @param type $args
     */
    public static function someCommandLineFunction() {

    }

    /**
     * For when you want to call it from a method instead of the CLI
     * 
     * @param string $command
     * @param array $arguments
     * @return string
     */
    public static function run($command,$arguments) {
        if ($command && $arguments) {
            self::arguments($arguments);
            return self::$command();
        }
    }     
 
}
