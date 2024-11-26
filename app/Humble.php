<?php
/*  ############################################################################
        __  __                __    __        ______           __
       / / / /_  ______ ___  / /_  / /__     / ____/___ ______/ /_____  _______  __
      / /_/ / / / / __ `__ \/ __ \/ / _ \   / /_  / __ `/ ___/ __/ __ \/ ___/ / / /
     / __  / /_/ / / / / / / /_/ / /  __/  / __/ / /_/ / /__/ /_/ /_/ / /  / /_/ /
    /_/ /_/\__,_/_/ /_/ /_/_.___/_/\___/  /_/    \__,_/\___/\__/\____/_/   \__, /
                                                                          /____/
    Everything goes through here

    ############################################################################ */
    require_once 'autoload.php';
    function underscoreToCamelCase($string, $first_char_caps=false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }    
    /**
     * Static factory used for all object creation
     *
     * PHP version 7.2+
     *
     * LICENSE:
     *
     * @category   Framework
     * @package    Base
     * @author     Original Author <rick@humbleprogramming.com>
     * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
     * @license    https://humbleprogramming.com/license.txt
     * @version    1.0.1
     * @since      File available since Version 1.0.1
     */
    class Humble  {
        private static $modules     = [];
        private static $controllers = [];
        private static $helpers     = [];  
        private static $response    = [];
        private static $namespace   = false;
        private static $controller  = false;
        private static $action      = false;
        private static $faker       = false;
        private static $cache       = false;
        private static $cacheConn   = false;
        private static $cacheFailed = false;
        private static $workflow    = [
        ];

        /**
         *
         */
        public function __construct() {
            //empty
        }

        /**
         * Necessary for basic level of debugging except for entities.  Do not over ride this method if you are in an entity
         *
         * @return system
         */
        public static function getClassName() {
            return __CLASS__;
        }

        /**
         * Abstract reference, useful in Twig templates since they can't handle static classes... you can assign to a variable this way
         *
         * @return type
         */
        public static function get() {
            return self;
        }

        /**
         * This gets the current translation table for which ever namespace is currently active
         *
         * @return array
         */
        public static function translateTable() {
            return Singleton::getTranslationTable();
        }

        /**
         * Puts a message onto a queue... Not used at this time but maybe re-enabled (LAZY) in the future
         *
         * @param type $queue
         * @param type $message
         * @return boolean
         */
        public static function mqput($queue=false,$message='') {
            if ($queue) {
                $channel = Singleton::getMQChannel();
                $channel->queue_declare($queue,false,false,false,false);
                $msg = new AMQPMessage($message,array('delivery_mode' => 2));
                return $channel->basic_publish($msg,'',$queue);
            }
            return false;
        }

        /**
         * Places a listener on a queue... Not used at this time but maybe re-enabled (LAZY) in the future
         *
         * @param type $queue
         * @param type $callback
         * @return boolean
         */
        public static function mqlisten($queue=false,$callback=false) {
            if ($queue && $callback) {
                $channel = Singleton::getMQChannel();
                $channel->queue_declare($queue,false,false,false,false);
                $channel->basic_consume($queue,'',false,true,false,false,$callback);
                while (count($channel->callbacks)) {
                    $channel->wait();
                }
                return true;
            }
            return false;
        }
        
        public static function toXML($iterator=false) {
            $xml = '';
            if ($iterator) {
                
            }
            return $xml;
        } 
        
        public static function toCSV($iterator=false) {
            $csv = '';
            if ($iterator) {
                
            }
            return $csv;
        }
        
        public static function toHTML($iterator=false) {
            $HTML = '';
            if ($iterator) {
                
            }
            return $HTML;
        }
        /**
         * A singleton to return our faker fake data generator
         * 
         * @return object
         */
        public static function fake() {
            return (self::$faker) ? self::faker : Humble::model('humble/faker');
        }
        
        /**
         * Returns the resource that is trying to be allocated broken into parts
         *
         * @param string $resource
         * @return string
         */
        public static function parseResource($resource) {
            if ($sep = strpos($resource,'/')) {
                $ns        = substr($resource,0,$sep);
                $namespace = (strtolower($ns)==='default') ? \Environment::namespace() : $ns;
                $resource  = [
                    'namespace' => $namespace,
                    'resource'  => str_replace('_','/',substr($resource,$sep+1))
                ];
            } else {
                //Should this be Environment::namespace() or self::_namespace()?
                $resource = [
                    'namespace' => Environment::namespace(),
                    'resource'  => str_replace('_','/',$resource)
                ];
            }
            return $resource;
        }

        /**
         * We are going to try to catch the printed response and redirect it to
         *  the console.  If you want to get some output out of then call this
         *  method
         */
        public static function response($text = false) {
            if ($text === false) {
                return self::$response;
            } else {
                if (is_array($text)) {
                    foreach ($text as $line) {
                        self::$response[] = $line;
                    }
                } else {
                    self::$response[] = $text;
                }
            }
        }

        /**
         * Returns either the specific requested instance of an entity of the parent class of all entities as a "Virtual" entity
         *
         * @param string $identifier
         * @return \Code\Framework\Humble\Entity\Entity
         */
        public static function entity($resource_identifier) {
            $identifier = self::parseResource($resource_identifier);
            $instance   = null;
            if ($module = self::module($identifier['namespace'])) {
                $str  = "Code/{$module['package']}/".str_replace("_","/",$module['entities'])."/".implode('/',array_map(function($word) { return ucfirst($word); }, explode('/',$identifier['resource'])));
                if (!$class = file_exists($str.".php") ? $str : false) {
                    $instance = new class(str_replace('/','\\','\\'.$str)) extends \Code\Framework\Humble\Entities\Unity {
                        private $anon_class = null;
                        public function __construct($a) {
                            parent::__construct();
                            $this->anon_class = $a;
                        }
                        public function getClassName() {  return $this->anon_class; }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$str);
                    $instance   = new $class();
                }
                if ($instance) {
                    $instance->_prefix($module['prefix'])->_namespace($identifier['namespace'])->_entity(str_replace('/','_',$identifier['resource']))->_isVirtual(!$class);
                }
            }
            return $instance;
        }

        /**
         * Returns an instance of a class or a "virtual class" if that class doesn't exist
         * 
         * @param string $resource_identifier
         * @param boolean $override
         * @param list $arguments
         * @return object
         */
        public static function model($resource_identifier,$override=false,...$arguments)  {
            $identifier     = self::parseResource($resource_identifier);
            $instance       = null;
            if ($module = self::module($identifier['namespace'],$override)) {
                $str   = "Code/{$module['package']}/".str_replace("_","/",$module['models'])."/".implode('/',array_map(function($word) { return ucfirst($word); }, explode('/',$identifier['resource'])));
                if (!$class = file_exists($str.".php") ? $str : false) {
                    $instance = new class(str_replace('/','\\',$str)) extends \Code\Framework\Humble\Models\Model {
                        private $anon_class = null;
                        public function __construct($a) {
                            $this->anon_class = $a;
                            $this->_isVirtual(true);
                            parent::__construct();
                        }
                        public function getClassName() {   return $this->anon_class; }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$class);
                    $instance   = new $class($arguments);
                }
                $instance->_prefix($module['prefix'])->_namespace($identifier['namespace'])->_isVirtual(!$class);
            }
            return $instance;
        }
        
        /**
         * A helper is different from a model in that it should not maintain state, or data, between invocations
         * 
         * @param string $resource_identifier
         * @return object
         */
        public static function helper($resource_identifier)  {
            $identifier     = self::parseResource($resource_identifier);
            $instance       = null;
            if (isset(self::$helpers[$resource_identifier])) {
                return self::$helpers[$resource_identifier];                    //Static, singleton style, allocation for helpers
            }
            if ($module = self::module($identifier['namespace'])) {
                $str   = "Code/{$module['package']}/".str_replace("_","/",$module['helpers'])."/".implode('/',array_map(function($word) { return ucfirst($word); }, explode('/',$identifier['resource'])));
                if (!$class = file_exists($str.".php") ? $str : false) {
                    $instance = new class(str_replace('/','\\',$str)) extends \Code\Framework\Humble\Helpers\Helper {
                        private $anon_class = null;
                        public function __construct($a) {
                            $this->anon_class = $a;
                            parent::__construct();
                        }
                        public function getClassName() {  return $this->anon_class; }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$class);
                    $instance   = new $class();
                }
                $instance->_namespace($identifier['namespace'])->_isVirtual(!$class);
            }  else {
               // \Log::general($identifier);
            }
            
            return self::$helpers[$resource_identifier] = $instance;
        }

        /**
         * Returns a reference to the a MongoDB Collection
         *
         * @param string $identifier
         * @return object
         */
        public static function collection($identifier) {
            $identifier = self::parseResource($identifier);
            $instance   = null;
            if ($module = self::module($identifier['namespace'])) {
                if ($module['mongodb']) {
                    $instance   = new \Code\Framework\Humble\Drivers\Mongo;//What is this for?
                    if (isset($identifier['resource'])) {
                        $instance->_collection(str_replace('/','_',$identifier['resource']));
                    }
                    $instance->_namespace($identifier['namespace']);
                }
            }
            return $instance;
        }

        /**
         *
         * @param string $dir The directory to recurse
         */
        private static function recurseDirectory($dir=false) {
            $files = [];
            if ($dir !== false) {
                $dh = dir($dir);
                while (($entry = $dh->read()) !== false) {
                    if (($entry == '.') || ($entry == '..')) {
                        continue;
                    }
                    if (is_dir($dir.'/'.$entry)) {
                        $files = array_merge($files,self::recurseDirectory($dir.'/'.$entry));
                    } else {
                        $files[] = $dir.'/'.$entry;
                    }
                }
            }
            return $files;
        }

        /**
         *  Returns all the current models associated to a namespace
         *
         *  @param string $namespace Namespace of the model containing the method
         */
        public static function getModels($namespace=false) {
            $models = [];
            $dir    = false;
            if ($namespace) {
                $module = self::module($namespace);
                if ($module) {
                    $dir = str_replace('_','/','Code/'.$module['package'].'_'.$module['models']);
                    if (is_dir($dir)) {
                        $models = self::recurseDirectory($dir);
                    }
                }
            }
            if ($dir) {
                $srch = array($dir.'/','.php','/');
                $repl = array('','','_');
                foreach ($models as $idx => $model) {
                    $models[$idx] = str_replace($srch,$repl,$model);
                }
            }
            return $models;
        }

        /**
         *  Returns all the physical entities associated to a namespace
         *
         *  @param string $namespace Namespace of the model containing the method
         */
        public static function getEntities($namespace=false) {
            $entities = [];
            $dir      = false;
            if ($namespace) {
                $module = self::module($namespace);
                if ($module) {
                    $dir = str_replace('_','/','Code/'.$module['package'].'_'.$module['entities']);
                    if (is_dir($dir)) {
                        $entities = self::recurseDirectory($dir);
                    }
                }
            }
            if ($dir) {
                $srch = array($dir.'/','.php','/');
                $repl = array('','','_');
                foreach ($entities as $idx => $entity) {
                    $entities[$idx] = str_replace($srch,$repl,$entity);
                }
            }
            return $entities;
        }

        /**
         *  Returns all the helpers associated to a namespace
         *
         *  @param string $namespace Namespace of the model containing the method
         */
        public static function getHelpers($namespace=false) {
            $helpers = [];
            $dir     = false;
            if ($namespace) {
                $module = self::module($namespace);
                if ($module) {
                    $dir = str_replace('_','/','Code/'.$module['package'].'_'.$module['helpers']);
                    if (is_dir($dir)) {
                        $helpers = self::recurseDirectory($dir);
                    }
                }
            }
            if ($dir) {
                $srch = array($dir.'/','.php','/');
                $repl = array('','','_');
                foreach ($helpers as $idx => $helper) {
                    $helpers[$idx] = str_replace($srch,$repl,$helper);
                }
            }
            return $helpers;
        }

        /**
         * A place for non-Humble framework compliant classes
         *
         * @param type $identifier
         * @param type $args
         * @return string
         */
        public static function getClass($identifier,$args=null) {
            $instance   = null;
            $class      = false;
            $data       = explode('/',$identifier);
            if (count($data) === 1) {
                $data[]  = $data[0];
                $data[0] = self::_namespace();
            }
            if ($module     = self::module($data[0])) {
                $location = 'Code/'.$module['package'].'/'.$module['module'].'/Classes/'.$data[1].'.php';
                if (file_exists($location)) {
                    include_once($location);
                    $instance = '\\Code\\'.$module['package'].'\\'.$module['module'].'\\Classes\\'.$data[1];
                    $instance = new $instance();
                } else {
                    $instance = "\\".$data[1]();  //Work on this later...  need to find out how to pass a variable set of arguments if needed on constructor
                }
            }
            return $instance;
        }

        /**
         * Used for workflow "bubbling"
         * 
         * @param string $workflowId
         * @return array
         */
        public static function pushWorkflow($workflowId) {
            return array_push(self::$workflow,$workflowId);
        }

        /**
         * Used for workflow "bubbling"
         * 
         * @param boolean $peek
         * @return string
         */
        public static function popWorkflow($peek=false) {
            if (!$peek) {
                return array_pop(self::$workflow);
            } else  {
                return self::$workflow[count(self::$workflow)-1];
            }
        }

        /**
         * From a blog post, a super fast caching mechanism for php objects, that doesn't seem to actually work
         * 
         * @param string $key
         * @param string $val
         * @return string
         */
        public static function opcache($key, $val=false) {
            if ($val) {
                $val = var_export($val, true);
                $val = str_replace('stdClass::__set_state', '(object)', $val);
                $tmp = "/var/www/tmp/$key." . uniqid('', true) . '.tmp';
                $didit = file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
                rename($tmp, "/var/www/tmp/$key");
            } else {
                include "/var/www/tmp/$key";
                return isset($val) ? $val : false;                
            }
        }
        
        /**
         * Caching is implemented here in the factory so that it can be easily switched out to another product (Redis, APC, etc) should it be necessary
         *
         * 'cacheFailed' is set if we tried to connect to memcached but it wasn't available.  It prevents trying multiple times
         * 
         * 
         * @TODO: Switch to a dependency injection so we can swap in  Redis if we want to
         * @param string $key
         * @param mixed $value
         * @return mixed
         */
        public static function cache($key,$value=null,$expire=0) {
            global $USE_REDIS;
            $retval = null; $args   = func_num_args(); $key = trim($key);
            if (\Environment::cachingEnabled()) {
                if (!self::$cache && !self::$cacheFailed) {
                    if ($cache_server = Environment::settings()->getCacheHost()) {
                        $USE_REDIS    = strpos($cache_server,'6379');
                        $cache_server = explode(':',$cache_server);
                        if (self::$cache = (($USE_REDIS) ? new \Redis() : new \Memcache())) {
                            if (!@self::$cacheConn = self::$cache->connect($cache_server[0],(isset($cache_server[1]) ? $cache_server[1] : (($USE_REDIS) ? 6379 :11211)))) {
                                self::$cacheFailed = true;
                            }
                        }
                    }
                }
                $serialNumber = Environment::serialNumber();
                if (!self::$cacheFailed) {
                    $retval = ($value !== null) ? (($USE_REDIS) ? self::$cache->set($serialNumber.'-'.$key,$value) : self::$cache->set($serialNumber.'-'.$key,$value,false,$expire)) : (($value === null) && ($args > 1) ? self::$cache->delete($serialNumber.'-'.$key) : self::$cache->get($serialNumber.'-'.$key) );
                }
            }
            return $retval;
        }

        /**
         * Relays an event to the Node.js Signaling Hub
         * 
         * @TODO: Switch from using the 'socketserver.txt' to the Humble.project file
         * @param string $eventName
         * @param array $data
         * @return boolean
         */
        public static function push($eventName,$data=[]) {
            $success = false;
            $project = Environment::getProject();
            if ($server = file_get_contents('../../socketserver_'.$project->namespace.'.txt')) {
                //change to read from Humble.project file
                $data['event'] = $eventName;
                $ch = curl_init($server.'/emit');
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data,'','&'));
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16");        
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);        
                $res        = curl_exec($ch);
                $info       = curl_getinfo($ch);
                $success    = ($info && isset($info['http_code']) && (($info['http_code'] == '200') || ($info['http_code'] == '100')));
            }
            return $success;
        }

        public static function emit($eventName=false,$data=[]) {
            $result = false;
            if ($eventName) {
                $trigger = \Event::getTrigger();
                $result = $trigger->emit($eventName,$data);
            }
            return $result;
        }
        /**
         * To protect yourself from bad impulses, access to the DB is restricted to instances of Unity (ORM) or a short list of privileged classes.  This is to encourage DAO style development
         *
         * @param \Code\Framework\Humble\Entities\Unity $callingClass
         * @return mixed
         */
        public static function connection($callingClass=false)        {
            if (!($conn = ($callingClass instanceof \Code\Framework\Humble\Entities\Unity))) {
                if ($callingClass) {
                    $name = $callingClass->getClassName();
                } else {
                    $name = (!isset($this)) ? self::getClassName() : null;
                }
                $shortList  = array('Humble','Code\Framework\Humble\Helpers\Installer','Code\Framework\Humble\Helpers\Updater','Code\Framework\Humble\Helpers\Compiler'); //These classes are allowed to specifically request a connection to the DB
                $conn       = in_array($name,$shortList);
            }
            return $conn ? Singleton::getMySQLAdapter() : $conn;
        }

        /**
         * Will return the configuration file contents for a module by a given namespace
         * 
         * @param string $namespace
         * @return \SimpleXMLElement
         */
        public static function config($namespace=false) {
            $struct = false;
            if ($namespace = $namespace ? $namespace : false) {
                if ($module = Humble::module($namespace)) {
                    if (file_exists($config = 'Code/'.$module['package'].'/'.$module['configuration'].'/config.xml')) {
                        $struct = new SimpleXMLElement(file_get_contents($config));
                    }
                }
            }
            return $struct->$namespace;
        }
        
        /**
         * Override says, "I don't care if it is disabled, give me the info"
         *   The override option is only to be set by utilities that need to
         *     enable/disable/uninstall the module.  Not by application logic.
         * 
         * @param string $namespace
         * @param boolean $override
         * @return array
         */
        public static function module($namespace=false,$override=false)  {
            if (!$namespace) {
                return [];
            }
            if (isset(self::$modules[$namespace])) {
                return self::$modules[$namespace];
            }
            if (!$data  = self::cache('module-'.$namespace)) {
                $db     = Humble::connection();
                $module = explode('/',$namespace);
                $query  = <<<SQL
                    select * from humble_modules
                      where namespace = '{$module[0]}'
SQL;
                $data = $db->query($query);                
                if (count($data) === 1) {
                    self::cache('module-'.$namespace,$data = self::$modules[$namespace] = $data[0]);
                }
            } else {
                self::$modules[$namespace] = $data;
            }
            return (isset($data['enabled']) && ($data['enabled']==='Y') ? $data : ($override ? $data : false));
        }

        /**
         * Tries to return the name of the call paired to a certain URI
         * 
         * @param type $namespace
         * @param type $URI
         * @return array
         */
        public static function findCallByURI($namespace=false,$URI=false) {
            $call = [];
            if ($namespace && $URI) {
                if ($module = self::module($namespace)) {
                    print_r($module); die();
                    yaml_parse(file_get_contents());
                }
            }
            return $call;
        }
        /**
         * Will attempt to get the cached last-compiled date for a particular controller... going to the DB if necessary
         * 
         * @param  string $identifier
         * @return array
         */
        public static function controller($identifier=false) {
            $data = [];
            if ($identifier) {
                if (isset(self::$controllers[$identifier]) && (self::$controllers[$identifier])) {
                    return self::$controllers[$identifier];
                }
                if (!$data  = self::cache('controller-'.$identifier)) {
                    $parts      = explode('/',$identifier);
                    $data       = self::entity('humble/controllers')->setNamespace($parts[0])->setController($parts[1])->fetch(true)->toArray();
                    if (count($data) === 1) {
                        self::cache('controller-'.$identifier,['compiled'=>$data[0]['compiled']]);
                    }
                } else {
                    self::$controllers[$identifier] = ['compiled'=>$data['compiled']];
                }
            }
            return $data;
        }

        /**
         * Returns a list of the folders/directories that modules are stored in... a "package" is just a directory containing modules
         * 
         * @return array
         */
        public static function packages()  {
            $packages   = [];
            $handler    = dir('Code/');
            while (($entry = $handler->read()) !== false) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir('Code/'.$entry)) {
                    $packages[] = $entry;
                }
            }
            return $packages;
        }

        /**
         * "Old" approach... 
         * 
         * @param type $package
         * @return type
         */
        public static function modules($package) {
            $modules= [];
            $directory = dir('Code/'.$package);

            while (($entry = $directory->read()) !== false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                $moduleDir = 'Code/'.$package.'/'.$entry;
                if (is_dir($moduleDir)) {
                    if (file_exists($moduleDir.'/etc/config.xml')) {
                        $modules[] = $entry;
                    }
                }
            }
            return $modules;
        }

        /**
         * Boxes a normal scalar string
         *
         * @param type $string
         * @return \Code\Framework\Humble\Helpers\HumbleString
         */
        public static function string($string='') {
            return new \Code\Framework\Humble\Helpers\HumbleString($string);
        }
        
        public static function array($arr=[]){
            return new \Code\Framework\Humble\Models\Arr($arr);
        }

        /**
         * Hybrid getter/setter for namespace. Used to set/retrieve the namespace set on the URL
         *
         * @param varied $arg
         * @return string
         */
        public static function _namespace($arg=false) {
            if ($arg !== false) {
                self::$namespace = $arg;
            }
            return self::$namespace;
        }

        /**
         * Hybrid getter/setter for namespace. Used to set/retrieve the namespace set on the URL
         *
         * @param varied $arg
         * @return string
         */
        public static function _controller($arg=false) {
            if ($arg !== false) {
                self::$controller = $arg;
            }
            return self::$controller;
        }
        public static function _action($arg=false) {
            if ($arg !== false) {
                self::$action = $arg;
            }
            return self::$action;
        }

        /**
         *
         */
        public function __clone()        {        }
        public function __wakeup()       {        }
    }
?>