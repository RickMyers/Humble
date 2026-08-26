<?php
/**
 * Static Factory methods for handling module level features
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
class Module {
    
    private   static $loaded    = [];
    public    static $flags     = [];
    private   static $useCache  = true;
    private   static $namespace = null;
    private   static $trueish   = [
        'Y'     => true,
        'TRUE'  => true,
        'ON'    => true,
        'YES'   => true,
        '1'     => true,
        1       => true
    ];    
    private   static $falseish  = [
        'N'     => true,
        'FALSE' => true,
        'OFF'   => true,
        'NO'    => true,
        '0'     => true,
        0       => true
    ];
    
    /**
     * Constructor
     */
    public function __construct() {
        //nop
    }

    /**
     * Setter/Getter for the all important namespace variable
     * 
     * @param type $ns
     * @return string?void
     */
    public static function namespace($ns=null) {
        if ($ns===null) {
            return self::$namespace;
        }
        self::$namespace = $ns;
    }
    
    /**
     * A quick function to determine if a booleanish [Y,YES,TRUE,ON,1,N,NO,FALSE,OFF,0] value was passed
     * 
     * @param type $value
     * @return type
     */
    private static function boolish($value=false) {
        if (isset(self::$trueish[(string)strtoupper($value)])) {
            $value = true;
        } else if (isset(self::$falseish[(string)strtoupper($value)])) {
            $value = false;
        }
        return $value;
    }
    
    /**
     * Primes the flags array, possibly getting values from cache first
     * 
     * @return bool
     */
    private static function loadFlags($namespace=null) {
        if ($namespace !== null) {
            if (self::$useCache) {
                self::$loaded = ((self::$flags = \Humble::cache('module_'.$namespace.'_flags')) !== null);
            }
            if (!self::$flags || !self::$useCache) {
                self::reload();
            } 
        }
        return self::$loaded[$namespace] ?? false;
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
    public static function reload($namespace=null) {
        $namespace = ($namespace) ? $namespace : (self::namespace() ? self::namespace() : false);
        if ($namespace) {
            self::$loaded[$namespace] = self::$loaded[$namespace] ?? false;
            if ($module = \Humble::module($namespace)) {
                if (file_exists($source = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['configuration']).'/flags.xml')) {
                    if (self::$flags[$namespace]    = json_decode(json_encode(simplexml_load_file($source)))) {
                        \Humble::cache('module_'.$namespace.'_flags',self::$flags[$namespace]);
                        self::$loaded[$namespace]   = true;
                    }
                }
            }
        }        
        return self::$loaded[$namespace] ?? false;
    }
    
    /**
     * Returns or sets a flag value, depends on arguments passed
     * 
     * @param type $name
     * @param type $value
     * @return string?null
     */
    public static function flag($name=false,$value=null) {
        $flag       = null;
        $namespace  = self::namespace();
        if ($name) {
            if (!self::$loaded[$namespace]) {
                self::loadFlags($namespace);
            }
            if ($value !== null) {
                if (isset(self::$flags[$namespace]->$name)) {
                    self::$flags[$namespace]->$name = $value;
                }
            } else {
                $flag = self::$flags[$namespace]->$name ?? null;
            }
        }
        return self::boolish($flag);
    }
    
    /**
     * Returns the contents of a resource file...
     * 
     * @TODO: Add an array that can be used for substitution
     * @param string $type
     * @param string $namespace
     * @param string $resource
     * @param bool $stripTags
     * @return string
     */
    public static function resource($type=false,$namespace=false,$resource=false,$substitution=[],$stripTags=false) {
        $result = '';
        if ($type && $namespace && $resource) {
            if ($module = Humble::module((string)$namespace)) {
                switch (strtolower($type)) {
                    case 'js'   :
                        break;
                    case 'php'  :
                        if (file_exists($file = 'Code/'.$module['package'].'/'.$module['module'].'/Resources/php/'.$resource.'.php')) {
                            $result = rtrim(file_get_contents($file));
                            if ($stripTags) {
                                $result = explode("\n",$result);
                                if (trim($result[0]) == '<?php') {
                                   array_shift($result);
                                   if (trim($result[count($result)-1]) == '?>') {
                                       array_pop($result);
                                   }
                                }
                                $result = implode("\n",$result);
                            }
                        }
                        break;
                    case 'sql'  :
                        break;
                    case 'template' : 
                        break;
                }
            }
        }
        return $result;
    }
}
