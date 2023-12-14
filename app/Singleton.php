<?php
/**
 * Manages singleton classes
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Original Author <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0.1
 * @since      File available since Version 1.0.1
 */
class Singleton
{
    private static $mySQLAdapter     = null;
    private static $mongoAdapter     = null;
    private static $settings         = null;
    private static $environment      = null;
    private static $helper           = [];
    private static $console          = null;
    private static $compiler         = null;
    private static $installer        = null;
    private static $monitor          = null;
    private static $updater          = null;
    private static $translationTable = null;
    private static $mappings         = [];
    private static $things           = [];
    private static $messages = [];
    private static $errors   = [];
    private static $warnings = [];
    private static $alerts   = [];

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * Daisy-chain call to the static classes destruct method
     */
    public function __destruct() {
        Singleton::destruct();
    }

    /**
     * Put code here to release any allocated resources
     */
    public static function destruct() {
    }
    /**
     *
     */
    public static function getMySQLAdapter()  {
        if (!isset(self::$mySQLAdapter)) {
            self::$mySQLAdapter = new \Code\Framework\Humble\Drivers\MySQL();
        }
        return self::$mySQLAdapter;
    }

    /**
     *
     */
    public static function getMongoAdapter()  {
        if (!isset(self::$mongoAdapter)) {
            self::$mongoAdapter = new \Code\Framework\Humble\Driver\Mongo();
        }
        return self::$mongoAdapter;
    }

    public static function getTranslationTable() {
        if (!isset(self::$translationTable)) {
            /*
             * Now load the translation table
             */
        }
        return self::$translationTable;
    }
    
    /**
     * Gets a little helper for 
     * 
     * @return object
     */
    public static function getConsole()  {
        return (!isset(self::$console)) ? new \Code\Framework\Humble\Helpers\Console() : self::$console;
    }

    /**
     *
     */
    public static function getCompiler()
    {
        if (!isset(self::$compiler)) {
            self::$compiler = new \Code\Framework\Humble\Helpers\Compiler();
        }
        return self::$compiler;
    }

    /**
     *
     */
    public static function getInstaller()
    {
        if (!isset(self::$installer)) {
            self::$installer = new \Code\Framework\Humble\Helpers\Installer();
        }
        return self::$installer;
    }

    /**
     *
     */
    public static function getMonitor()
    {
        if (!isset(self::$monitor)) {
            self::$monitor = new \Code\Framework\Humble\Helpers\Monitor();
        }
        return self::$monitor;
    }
    
    /**
     *
     */
    public static function getUpdater() {
        if (!isset(self::$updater)) {
            self::$updater = new \Code\Framework\Humble\Helpers\Updater();
        }
        return self::$updater;
    }

    /**
     *  @TODO:  Determine if this is obsolete... 
     */
    public static function getHelper($base,$name='Data')    {
        //hit namespace for helper location.... then go after it
        if (!isset(self::$helper[$name])) {
            $helperClass = $base.'_'.$name.'.php';
            $helperClass = (file_exists($helperClass)) ? $helperClass : '\Code\Framework\Humble\Helpers\Helper' ;
            self::$helper[$name] = new $helperClass();
        }
        return self::$helper[$name];
    }

    /**
     * Allows us to manage arbitrary elements/objects that don't need to hold state
     * 
     * @param type string
     * @param type object
     * @return object
     */
    public static function manage($alias=false,$thing=null) {
        if ($alias && $thing) {
            self::$things[$alias] = $thing;
        } else if ($alias && !$thing && isset(self::$things[$alias])) {
            return self::$things[$alias];
        }
        return $thing;
    }
    
    /**
     * Use this one
     */
    public static function getSettings()    {
        if (!isset(self::$settings)) {
            self::$settings = new \Settings();
        }
        return self::$settings;
    }

    /**
     * Allows you to override a value in the Settings class
     * 
     * @param type $node
     * @param type $values
     * @return type
     */
    public static function setSettings($node,$values=array())  {
        return self::$settings[$node] = $values;
    }

    /**
     *
     */
    public static function getEnvironment()
    {
        if (!isset(self::$environment)) {
            self::$environment = new \Settings();
        }
        return self::$environment;
    }

    public static function mappings($mappings = null) {
        if ($mappings !== null) {
            self::$mappings = $mappings;
        }
        return self::$mappings;

    }

    /**
     * Stores a normal message
     * 
     * @param string $message
     * @return $this
     */
    public static function log($message=false) {
        if ($message) {
            self::$messages[] = (is_object($message)) ? print_r($message,true) : $message;
        } else {
            $messages         = self::$messages;                                        //so we only return the messages once on first class destruct
            self::$messages   = [];
            return $messages;
        }
    }

    /**
     * Stores an error message
     * 
     * @param string $message
     * @return $this
     */
    public static function error($message=false) {
        if ($message) {
            self::$errors[] = (is_object($message)) ? print_r($message,true) : $message;
        } else {
            $messages       = self::$errors;                                        //so we only return the messages once on first class destruct
            self::$errors  = [];
            return $messages;
        }
    }
    
    /**
     * Stores a warning message
     * 
     * @param string $message
     * @return $this
     */
    public static function warn($message=false) {
        if ($message) {
            self::$warnings[] = (is_object($message)) ? print_r($message,true) : $message;
        } else {
            $messages       = self::$warnings;                                        //so we only return the messages once on first class destruct
            self::$warnings = [];
            return $messages;
        }
    }
    
    /**
     * Stores a message meant for an alert prompt
     * 
     * @param string $message
     * @return $this
     */
    public static function alert($message=false) {
        if ($message) {
            self::$alerts[] = (is_object($message)) ? print_r($message,true) : $message;
        } else {
            $messages       = self::$alerts;                                        //so we only return the messages once on first class destruct
            self::$alerts   = [];
            return $messages;
        }
    }        
    /**
     *
     */
    public function __clone()        {        }
    public function __wakeup()       {        }
}
