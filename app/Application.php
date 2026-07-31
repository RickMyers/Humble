<?php
/**
 * Static Factory methods for handling application level features
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
class Application {
    
    private   static $loaded    = false;
    public    static $flags     = [];
    private   static $useCache  = true;
    
    /**
     * Constructor
     */
    public function __construct() {
        //nop
    }

    /**
     * Primes the flags array, possibly getting values from cache first
     * 
     * @return bool
     */
    private static function loadFlags() {
        if (self::$useCache) {
            self::$loaded = ((self::$flags = \Humble::cache('application-flags')) !== null);
        }
        if (!self::$flags || !self::$useCache) {
            self::reload();
        } 
        return self::$loaded;
    }
    
    /**
     * Overly complicated getter/setter for whether to use the cache or not
     * 
     * @param type $useCache
     * @return type
     */
    public static function cache($useCache=null) {
        return self::$useCache = ($useCache===null) ? true : ($useCache === true ? true : false);
    } 
    
    
    /**
     * Loads/Reloads flag cache from file, storing a copy in cache for future loads
     * 
     * @return bool
     */
    public static function reload() {
        if ($namespace  = \Environment::project('namespace')) {
            if ($module = \Humble::module($namespace)) {
                if (file_exists($source = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['configuration']).'/flags.xml')) {
                    if (self::$flags    = json_decode(json_encode(simplexml_load_file($source)))) {
                        \Humble::cache('application-flags',self::$flags);
                        self::$loaded   = true;
                    }
                }
            }
        }        
        return self::$loaded;
    }
    
    /**
     * Returns or sets a flag value, depends on arguments passed
     * 
     * @param type $name
     * @param type $value
     * @return string?null
     */
    public static function flag($name=false,$value=null) {
        $flag = null;
        if ($name) {
            if (!self::$loaded) {
                self::loadFlags();
            }
            if ($value !== null) {
                if (isset(self::$flags->$name)) {
                    self::$flags->$name = $value;
                }
            } else {
                $flag = self::$flags->$name ?? null;
            }
        }
        return $flag;
    }
}