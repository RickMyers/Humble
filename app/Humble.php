<?php
/*  ############################################################################
       ___                  _      ______         _
      |_  |                (_)     |  ___|       | |
        | | __ _ _ ____   ___ ___  | |_ __ _  ___| |_ ___  _ __ _   _
        | |/ _` | '__\ \ / / / __| |  _/ _` |/ __| __/ _ \| '__| | | |
    /\__/ / (_| | |   \ V /| \__ \ | || (_| | (__| || (_) | |  | |_| |
    \____/ \__,_|_|    \_/ |_|___/ \_| \__,_|\___|\__\___/|_|   \__, |
                                                                 __/ |
                                                                |___/
    Everything goes through here

    ############################################################################ */
    require_once 'autoload.php';
    /**
     * Static factory used for all object creation
     *
     * PHP version 7.2+
     *
     * LICENSE:
     *
     * @category   Framework
     * @package    Core
     * @author     Original Author <rickmyers1969@gmail.com>
     * @copyright  2007-Present, Rick Myers <rick@enicity.com>
     * @license    http://license.humble.enicity.com
     * @version    1.0.1
     * @since      File available since Version 1.0.1
     */
    class Humble  {
        private static $modules     = array();
     // private static $helpers     = array();  //we are not keeping these static anymore
        private static $response    = array();
        private static $namespace   = false;
        private static $controller  = false;
        private static $action      = false;
        private static $cache       = false;
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
         * Puts a message onto a queue...
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
         * Places a listener on a queue
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
         * @return \Code\Base\Humble\Entity\Entity
         */
        public static function getEntity($identifier) {
            $instance   = null;
            $class      = false;
            $virtual    = false;
            $entity     = explode('/',$identifier);
            if (count($entity) === 1) {
                $entity[]  = $entity[0];
                $entity[0] = self::_namespace();
            }
            $module     = self::getModule($identifier);
            if ($module) {
                $st         = explode('_',$entity[1]);
                foreach ($st as $idx => $term) {
                    $st[$idx] = ucfirst($term);
                }
                $ast        = implode('/',$st);
                $dir        = str_replace("_","/",$module['entities'])."/".$ast;
                $str        = "Code/{$module['package']}/{$dir}";
                $class      = file_exists($str.".php") ? $str : false;
                if (!$class) {
                    $instance = new class(str_replace('/','\\','\\'.$str)) extends Code\Base\Humble\Entity\BaseObject {
                        private $anon_class = null;
                        public function __construct($a) {
                            parent::__construct();
                            $this->anon_class = $a;
                        }
                        public function getClassName() {
                            return $this->anon_class;
                        }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$str);
                    $instance   = new $class();
                }
                $instance->_prefix($module['prefix'])->_namespace($entity[0])->_entity($entity[1])->_isVirtual(!$class);
            }
            return $instance;
        }

        /**
         * Returns the contents of the project file of false if the project hasn't been created yet
         *
         * @return object
         */
        public static function getProject() {
            return (file_exists('../Humble.project')) ? json_decode(file_get_contents('../Humble.project'),true) : false;
        }

        /**
         *
         */
        public static function getProjectConfiguration($module=false) {
            $xml    = null;
            $data   = null;
            if ($module) {
                //get module info and
            } else {
                $data   = Environment::getProject();
            }

            $res    = Humble::getEntity('humble/modules')->setNamespace($data->namespace)->load();
            $source = 'Code/'.$res['package'].'/'.$res['configuration'].'/config.xml';
            if (file_exists($source)) {
                $xml =  new \SimpleXMLElement(file_get_contents($source));
            }
            return $xml;

        }

        /**
         * Returns a reference to the a MongoDB Collection
         *
         * @param type $identifier
         * @return \Code\Base\Humble\Models\MongoDB
         */
        public static function getCollection($identifier) {
            $instance   = null;
            $entity     = explode('/',$identifier);
            if (count($entity) === 1) {
                $entity[]  = $entity[0];
                $entity[0] = self::_namespace();
            }
            $module     = self::getModule($entity[0]);
            if ($module) {
                if ($module['mongodb']) {
                    $instance   = new \Code\Base\Humble\Models\Mongo($module['mongodb']);
                    if (isset($entity[1])) {
                        $instance->_collection($entity[1]);
                    }
                    $instance->_namespace($entity[0]);
                }
            }
            return $instance;
        }

        /**
         *
         */
        public static function getModel($identifier,$override=false)  {
            $instance       = null;
            $model          = explode('/',$identifier);
            if (count($model) === 1) {
                $model[]    = $model[0];
                $model[0]   = self::_namespace();
            }
            $module         = self::getModule($identifier,$override);
            if ($module) {
                $className  = ucfirst($model[1]);
                $dir        = str_replace("_","/",$module['models']);
                $str        = "Code/{$module['package']}/{$dir}/{$className}";
                $class      = file_exists($str.".php") ? $str : false;
                if (!$class) {
                    $instance = new class(str_replace('/','\\',$str)) extends Code\Base\Humble\Model\BaseObject {
                        private $anon_class = null;
                        public function __construct($a) {
                            $this->anon_class = $a;
                            parent::__construct();
                        }
                        public function getClassName() {
                            return $this->anon_class;
                        }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$class);
                    $instance   = new $class();
                }
                $instance->_prefix($module['prefix'])->_namespace($model[0])->_isVirtual(!$class);
            }
            return $instance;
        }

        /**
         *
         * @param string $dir The directory to recurse
         */
        private static function recurseDirectory($dir=false) {
            $files = array();
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
            $models = array();
            $dir    = false;
            if ($namespace) {
                $module = self::getModule($namespace);
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
            $entities = array();
            $dir      = false;
            if ($namespace) {
                $module = self::getModule($namespace);
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
            $helpers = array();
            $dir     = false;
            if ($namespace) {
                $module = self::getModule($namespace);
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
         *
         */
        public static function getHelper($identifier)  {
            $instance   = null;
            $class      = false;
            $helper     = explode('/',$identifier);
            $virtual    = false;
            if (count($helper) === 1) {
                $helper[]  = $helper[0];
                $helper[0] = self::_namespace();
            }
            $module     = self::getModule($helper[0]);
            $helper[1]  = ($helper[1] == 'string') ? 'HumbleString' : $helper[1] ; //lil hack  PHP V7 doesn't allow for us to name a class "String"
            if ($module) {
                $className  = ucfirst($helper[1]);
                $dir        = str_replace("_","/",$module['helpers']);
                $str        = "Code/{$module['package']}/{$dir}/{$className}";
                $class      = file_exists($str.".php") ? $str : false;
                if (!$class) {
                    $instance = new class(str_replace('/','\\',$str)) extends \Code\Base\Humble\Helper\BaseObject {
                        private $anon_class = null;
                        public function __construct($a) {
                            $this->anon_class = $a;
                            parent::__construct();
                        }
                        public function getClassName() {
                            return $this->anon_class;
                        }
                    };
                } else {
                    $class      = str_replace('/','\\','\\'.$class);
                    $instance   = new $class();
                }
                $instance->_namespace($helper[0])->_isVirtual(!$class);
            }
            return $instance;
        }

        /**
         * A place for non-Humble framework classes
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
            if ($module     = self::getModule($data[0])) {
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

        public static function pushWorkflow($workflowId) {
            return array_push(self::$workflow,$workflowId);
        }

        public static function popWorkflow($peek=false) {
            if (!$peek) {
                return array_pop(self::$workflow);
            } else  {
                return self::$workflow[count(self::$workflow)-1];
            }
        }


        /**
         * Caching is implemented here in the factory so that it can be easily switched out to another product (Redis, APC, etc) should it be necessary
         *
         * 'cacheFailed' is set if we tried to connect to memcached but it wasn't available.  It prevents trying multiple times
         *
         * @param string $key
         * @param mixed $value
         * @return mixed
         */
        public static function cache($key,$value=null) {
            $retval = null;
            $conn   = false;
            if (\Environment::cachingEnabled()) {
                if (!self::$cache && !self::$cacheFailed) {
                    if ($cache_server = Environment::settings()->getCacheHost()) {
                        $cache_server = explode(':',$cache_server);
                        if (self::$cache = new Memcache()) {
                            @$conn = self::$cache->connect($cache_server[0],(isset($cache_server[1]) ? $cache_server[1] : 11211));
                            if (!$conn) {
                                self::$cacheFailed = true;
                            }
                        }
                    }
                }
                if ($conn) {
                    if ($value !== null) {
                        $retval = self::$cache->set($key,$value);
                    } else {
                        $retval = self::$cache->get($key);
                    }
                }
            }
            return $retval;
        }

        /**
         * To protect yourself from bad impulses, access to the DB is restricted to instances of Entity the object or a short list of privileged classes.  This is to encourage DAO style development
         *
         * @param \Code\Base\Humble\Entity\BaseObject $callingClass
         * @return mixed
         */
        public static function getDatabaseConnection($callingClass=false)        {
            if (!($conn = ($callingClass instanceof \Code\Base\Humble\Entity\BaseObject))) {
                if ($callingClass) {
                    $name = $callingClass->getClassName();
                } else {
                    $name = (!isset($this)) ? self::getClassName() : null;
                }
                $shortList  = array('Humble','Code\Base\Humble\Helpers\Installer','Code\Base\Humble\Helpers\Updater','Code\Base\Humble\Helpers\Compiler'); //These classes are allowed to specifically request a connection to the DB
                $conn       = in_array($name,$shortList);
            }
            return $conn ? Singleton::getMySQLAdapter() : $conn;
        }

        /**
         * Override says, "I don't care if it is disabled, give me the info"
         *   The override option is only to be set by utilities that need to
         *     enable/disable/uninstall the module.  Not by application logic.
         */
        public static function getModule($identifier,$override=false)  {
            if (isset(self::$modules[$identifier])) {
                return self::$modules[$identifier];
            }
            if (!$data = Humble::cache('module-'.$identifier)) {
                $db     = Humble::getDatabaseConnection();
                $module = explode('/',$identifier);
                $query = <<<SQL
                    select * from core_modules
                      where namespace = '{$module[0]}'
SQL;
                $data = $db->query($query);
                if (count($data) == 1) {
                    Humble::cache('module-'.$identifier,$data = self::$modules[$identifier] = $data[0]);
                } else {
                    $data = null;
                }
            }
            return (isset($data['enabled']) && ($data['enabled']==='Y') ? $data : ($override ? $data : false));
        }

        /**
         *
         */
        public static function getController($identifier) {
            $db         = Humble::getDatabaseConnection();
            $controller = explode('/',$identifier);
            $query      = <<<SQL
                select * from core_controllers
                  where namespace = '{$controller[0]}'
                    and controller = '{$controller[1]}'
SQL;
            $data = $db->query($query);
            return (count($data) == 1) ? $data[0] : null;
        }

        /**
         *
         * @return type
         */
        public static function getPackages()  {
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
         *
         */
        public static function getModules($package) {
            $modules= array();
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
         *
         */
        public static function getNamespaces($package=false) {
            $namespaces = array();
            if ($package) {
                $db    = Humble::getDatabaseConnection();
                $query = <<<SQL
                    select namespace from core_modules where package = '{$package}' and enabled = 'Y'
SQL;
                foreach ($db->query($query) as $idx => $result) {
                    $namespaces[] = $result['namespace'];
                }
            } else {
                $namespaces[] = "No package specified";
            }
            return $namespaces;
        }

        public static function hash($number) {
            require_once('../lib/Hashids/lib/Hashids/HashGenerator.php');
            require_once('../lib/Hashids/lib/Hashids/Hashids.php');
            $t = (new Hashids\Hashids(uniqid('F',true)))->encode($number);
            return  $t;
        }

        /**
         * Boxes a normal scalar string
         *
         * @param type $string
         * @return \Code\Base\Humble\Helpers\HumbleString
         */
        public static function string($string) {
            return new \Code\Base\Humble\Helpers\HumbleString($string);
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