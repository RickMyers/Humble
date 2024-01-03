<?php
require_once 'cli/CLI.php';
class Component extends CLI 
{
 
    /**
     * Compiles a controller 
     */
    public static function compile() {
        $args       = self::arguments();
        $file       = $args['file'];
        print($file."\n");
        $compiler   = \Environment::getCompiler();
        $compiler->compileFile($file);
    }

    /**
     * Builds a controller
     */
    public static function build() {
        $util = Humble::model('admin/utility',true);
        foreach (self::arguments() as $field => $value) {
            $method  = 'set'.self::underscoreToCamelCase($field,true);
            $util->$method($value);
        }
        $use_landing = $util->getLanding() ? true : false;
        $util->createController($use_landing,true);
    }
    
    /**
     * Builds a component, such as Model, Entity, or Helper
     */
    public static function create() {
        $args       = self::arguments();
    }
}


