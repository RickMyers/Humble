<?php
/**
 * Manages Environment Information and Resources
 *
 * PHP version 5.6+
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
class Environment {

    private static $isAjax      = false;
    private static $settings    = false;
    private static $session_id  = false;
    private static $status      = false;
    private static $application = false;
    private static $project     = false;

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Necessary for basic level of debugging except for entities.  Do not over ride this method if you are in an entity
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Returns the user id of the current person
     *
     * @return int
     */
    public static function user() {
        return (isset($_SESSION['uid']) ? $_SESSION['uid'] : false);
    }

    /**
     * Try to get the public facing route list from cache, but fall back to reading from file if not found in cache
     * 
     * @param type $project
     * @return object
     */
    public static function routes($project=false) {
        $project = ($project) ? $project : self::project();                  //did you pass project information or do I have to load it myself
        $routes  = \Humble::cache('public_routes');                             //are routes cached?
        if (!$routes) {
            if (!file_exists($file = 'Code/'.$project->package.'/'.$project->module.'/etc/public_routes.json')) {
                header("Location: /install.php");
                die();
            }
            $routes = json_decode(file_get_contents($file));
        }
        return $routes??[];
    }
    
    /**
     * Attempts to put the route aliases into the cache for faster retrieval
     * 
     * @param type $project
     */
    public static function recacheRouteAliases($project=false) {
        $result = false;
        $project = ($project) ? $project : self::project();
        if (file_exists($alias_file = 'Code/'.$project->package.'/'.$project->module.'/etc/route_aliases.json')){
            Humble::cache('humble_route_aliases',json_decode(file_get_contents($alias_file),true));
            $result = true;
        }
        return $result;
    }
    
    /**
     * Returns the location of the modules root, use this instead of hardcoding module paths
     *
     * @param string $namespace
     * @return boolean
     */
    public static function getRoot($namespace=false) {
        $root = false;
        if ($namespace) {
            $module = Humble::module($namespace);
            if ($module) {
                $name = (strpos($module['models'],"_")) ? explode('_',$module['models']) : explode('/',$module['models']);
                $root = 'Code/'.$module['package'].'/'.$name[0];
            }
        }
        return $root;
    }

    /**
     * Returns the default date format from the application.xml meta data file
     * 
     * @return string
     */
    public static function  getDateFormat() {
        $defaults    = self::application('default');
        return (isset($defaults['date_format'])) ? $defaults['date_format'] : 'Y-m-d';
    }

    /**
     * Returns the default date format from the application.xml meta data file
     * 
     * @return string
     */
    public static function  getTimeFormat() {
        $defaults    = self::application('default');
        return (isset($defaults['time_format'])) ? $defaults['time_format'] : 'H:i:s';
    }
    
    /**
     * Returns the default date format from the application.xml meta data file
     * 
     * @return string
     */
    public static function  getTimestampFormat() {
        $defaults    = self::application('default');
        return (isset($defaults['timestamp_format'])) ? $defaults['timestamp_format'] : 'Y-m-d H:i:s';
    }
    
    /**
     * Will format a date according to the default date format set in the application.xml file
     * 
     * @param string $date
     * @return mixed
     */
    public static function formatDate($date=false) {
        $date        = ($date) ? $date : date('Y-m-d');
        $defaults    = self::application('default');
        $date_format = isset($defaults['date_format']) ? $defaults['date_format'] : false;
        return $date_format ? date($date_format, strtotime($date)) : $date;
    }
    
    /**
     * Will format a date according to the default date format set in the application.xml file
     * 
     * @param string $date
     * @return mixed
     */
    public static function formatTime($date=false) {
        $date        = ($date) ? $date : date('H:i:s');
        $defaults    = self::application('default');
        $time_format = isset($defaults['time_format']) ? $defaults['time_format'] : false;
        return $time_format ? date($time_format, strtotime($date)) : $date;
    }
    
    /**
     * Will format a date according to the default date format set in the application.xml file
     * 
     * @param string $date
     * @return mixed
     */
    public static function formatTimestamp($date=false) {
        $date        = ($date) ? $date : date('Y-m-d H:i:s');
        $defaults    = self::application('default');
        $time_format = isset($defaults['timestamp_format']) ? $defaults['timestamp_format'] : false;
        return $time_format ? date($time_format, strtotime($date)) : $date;
    }
    
    /**
     * Returns the namespace of the primary (first) module identified when the installation took place
     * 
     * @return string
     */
    public static function namespace() {
        if (!self::$project) {
           self::$project = self::project();
        }
        return (isset(self::$project->namespace)) ? self::$project->namespace : '';        
    }
    
    /**
     * Combines the protocol and server name to construct the complete host name
     *
     * @return string
     */
    public static function getHost() {
        $protocol   = isset($_SERVER['HTTPS']) ? ((strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http') : 'http';
        $host       = $_SERVER['SERVER_NAME'];
        return $protocol.'://'.$host;
    }

    /**
     * Will start a program in the background
     * 
     * @param type $program
     * @param type $arguments
     * @return type
     */
    public static function start($program=false,...$arguments) {
        $arg_str = '';
        foreach ($arguments as $argument) {
            $arg_str .= ' '.$argument;
        }
        $cmd_str = 'nohup '.$program.$arg_str.' > /dev/null 2>&1 &';
        exec($cmd_str,$result,$rc);
        return $rc;
    }
    
    /**
     * Returns the location of the PHP executable
     * 
     * @return string
     */
    public static function PHPLocation() {
        if (!self::$application) {
             self::loadApplicationMetaData(true);
        }
        return str_replace(["\r","\n"],["",""],((strncasecmp(PHP_OS, 'WIN', 3) === 0) ? `where php.exe` : `which php`));
    }
    
    /**
     * Returns if the Micro-Services Architecture Router Flag is set and activated
     * 
     * @return boolean
     */
    public static function MSARouter() {
        if (!self::$application) {
            self::loadApplicationMetaData(true);
        }
        return (isset(self::$application->msa->router)
                && ((self::$application->msa->router===1)
                || (self::$application->msa->router === 'Y')));
    }

    /**
     * Returns the current State of the system
     * 
     * @return boolean
     */
    public static function state($dontUseCache=false) {
        if (!self::$application) {
            self::loadApplicationMetaData($dontUseCache);
        }
        return isset(self::$application->state) ?  self::$application->state : 'Unknown';
    }

    /**
     * Returns whether or not the current user has the admin flag set
     * 
     * @return type
     */
    public static function userIsAdmin() {
        return $_SESSION['admin_id'] ?? false;
    }
    
    /**
     * Logs the admin in as a general user
     */
    public static function logAdminIn() {
        $logged_in = false;
        if ($user  = self::session('user')) {
            if ($data = Humble::entity('default/users')->setUserName($user['user_name'])->load(true)) {
                self::session('uid',$data['id']);
                self::session('user',$data);
                $logged_in = true;
            }
        }
        return $logged_in;
    }
    
    /**
     * Returns true if the system is in PRODUCTION, which means certain features will be turned on
     * 
     * @return boolean
     */
    public static function isProduction() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (isset(self::$application->state) && (self::$application->state === 'PRODUCTION'));
    }

    /**
     * Returns true if the system state is not present or if it is present then it must equal 'DEVELOPMENT'
     * 
     * @return boolean
     */
    public static function isDevelopment() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (!isset(self::$application->state) || (isset(self::$application->state) && (self::$application->state==='DEVELOPMENT')));
    }

    /**
     * Returns true if the system is in TEST, which means certain features will be turned on
     * 
     * @return boolean
     */
    public static function isTest() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (isset(self::$application->state) && (self::$application->state === 'TEST'));
    }

    /**
     * Returns true if the system is in DEBUG, which means certain features will be turned on
     * 
     * @return boolean
     */
    public static function isDebug() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (isset(self::$application->state) && (self::$application->state==='DEBUG'));
    }
    
    /**
     * Returns true if the system has been put into a state requiring extensive debugging
     * 
     * @return boolean
     */
    public static function isActiveDebug() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (isset(self::$application->state) && (self::$application->state === 'DEBUG'));
    }
    
    /**
     * We are using Rain 3 for internal "in-line" templating
     *
     * @return \Rain\Tpl
     */
    public static function getInternalTemplater($root='',$extension='rain',$cache=false) {
        $root   = ($root) ? $root : getcwd();
        $root   = (substr($root,-1,1)==='/') ? $root : $root.'/';
        $cache  = ($cache) ? $cache : $root.'cache/';
        $config = array(
            'tpl_dir'   => $root,
            'tpl_ext'   => $extension,
            'cache_dir' => $cache
        );
        \Rain\Tpl::configure($config);
        return new \Rain\Tpl;
    }
    
    /**
     * Returns the complete Application Meta Data as an XML object
     * 
     * @return type
     */
    public static function applicationXML() {
        $project = self::project();
        $file    = 'Code/'.$project->package.'/'.$project->module.'/etc/application.xml';
        return file_exists($file) ? simplexml_load_string(file_get_contents($file)) : new stdClass();
    }
    
    /**
     * Returns the physical location of the application Meta Data file 
     *
     * @return string
     */
    public static function applicationXMLLocation() {
        $project = self::project();
        return 'Code/'.$project->package.'/'.$project->module.'/etc/application.xml';
    }
    
    /**
     * Application XML has changed and we need to recache it
     */
    public static function recacheApplication() {
        self::$application = json_decode(json_encode(self::applicationXML()));
        Humble::cache('application',self::$application);        
    }
    
    /**
     * Either returns the previously loaded metadata or loads and returns it
     * 
     * @return Array
     */
    public static function loadApplicationMetaData($dontUseCache=false) {
        if ($dontUseCache) {
            return self::$application = json_decode(json_encode(self::applicationXML()));
        } else {
            if (!self::$application = Humble::cache('application')) {
                self::recacheApplication();
            }
            return self::$application;
        }
    }

    /**
     * Returns the application status as either fresh XML object (passed value of true) or what is stored in the cache (normal mode)
     *
     * @return type
     */
    public static function status($dontUseCache=false) {
        return self::loadApplicationMetaData($dontUseCache);
    }

    /**
     * Retrieves the current runtime settings, or if not set, instantiates the runtime settings class and returns that
     *
     * @return \Settings
     */
    public static function settings() {
        return self::$settings ? self::$settings : new \Settings();
    }

    /**
     * Evaluates a flag to see if it is one of the "false" values, otherwise anything else is regarded as true
     * 
     * @param mixed $val
     * @return bool
     */
    protected static function truthyOrFalsey($val='') {
        $t_o_f = true;
        switch (strtoupper($val)) {
            case "N":
            case "NO":
            case "OFF":
            case "FALSE":
            case 0:
            case null:
            case "0":
            case "":
                $t_o_f = false;
            
            default:
                break;
        }
        return $t_o_f;
    }
    
    /**
     * Returns whether a flag is on (true) or off (false)
     * 
     * @param string $flag
     * @return boolean
     */
    public static function flag($flag=false) {
        $value = '';
        if ($flag) {
            if (!self::$application) {
                self::loadApplicationMetaData();
            }
            $value = self::$application->flags->$flag ?: null;
           // return $value;
            print($value);
        }
        return self::truthyOrFalsey($value);
    }
    
    /**
     * Returns if caching is enabled.  Caching is no longer a soft requirement. Trying to make it selectable will lead to infinite loops
     *
     * @return boolean
     */
    public static function cachingEnabled() {
        if (!self::$application) {
            self::loadApplicationMetaData(true);
        }
        return (isset(self::$application->status) && isset(self::$application->status->caching) && (int)self::$application->status->caching);
    }
    
    /**
     * Returns the serial number of this application.  The serial number is used in cryptography and caching.  Arbitrarily changing the serial number can cause the cache to be unreadable, as well as the loss of all secrets in the Secret Manager
     * 
     * @return type
     */
    public static function serialNumber() {
        if (!self::$project) {
           self::$project = self::project();
        }
        return (isset(self::$project->serial_number)) ? self::$project->serial_number : '';
    }
    
    /**
     * This is a wrapper for the session variable.  It will either set a session variable, return a session variable, or return the session array if no parameter is passed
     *
     * @param type $variable
     * @param type $value
     * @return type
     */
    public static function session($variable=null,$value=null) {
        if ($variable !== null) {
            if ($value !== null) {
                $_SESSION[$variable] = $value;
            } else {
                return (isset($_SESSION[$variable]) ? $_SESSION[$variable] : null);
            }
        } else {
            return $_SESSION;
        }
    }

    /**
     * By default, PHP locks the session data down so only one process can write to it at a time.  By calling unblock, you are capturing the session I
     * and closing the write-lock on the session data.  This then allows for parallel access to the session data.  You can still read from the session
     * while something is unblocked, and you can re-establish a lock on the session for write by calling the "Environment::block()" method
     */
    public static function unblock() {
        self::$session_id = session_id();
        session_write_close();
    }

    /**
     * By default, PHP locks the session data down so only one process can write to it at a time.  By calling block, you are restablishing the session lock
     */
    public static function block() {
        if (self::$session_id) {
            session_id(self::$session_id);
        }
        session_start();
    }

    /**
     * Call this prior to performing any action to see if the system has been taken offline.  It will route you to the login screen with the appropriate message
     *
     * @return string
     */
    public static function statusCheck($namespace=false,$controller=false,$method=false) {
        $status = false;
        if (($namespace==='humble') && ($controller==='system') && ($method==='active')) {
            return false;
        }
        if (!self::$application) {
            self::loadApplicationMetaData();
            if (!empty(self::$application)) {
                if (isset(self::$application->status)) {
                    if (isset(self::$application->status->quiescing) && ((int)self::$application->status->quiescing)) {
                        $status = "System is going offline...";
                    } else if (isset(self::$application->status->enabled) && ((int)self::$application->status->enabled)) {
                        //nop; everything is good
                    } else {
                        $status = "System is currently offline";
                    }
                } else {
                    $status = "The application is not correctly configured.  Correct the application configuration file and try again";
                }
            } else {
                $status = "There is an error in the application configuration file";
            }
        }
        if ($status !== false) {
            header("location: /index.html?message=".$status);
            die();
        }
        return (isset(self::$application->status->authorization) && (int)self::$application->status->authorization->enabled); //this will always, or should always, be false
    }

    /**
     * Returns whether this call was made through an AJAX request
     *
     * @param type $arg
     * @return type
     */
    public static function isAjax($arg=null) {
        if ($arg !== null) {
            self::$isAjax = $arg;
        } else {
            return self::$isAjax;
        }
    }

    /**
     * Returns the row from the user table that corresponds with the id
     *
     * @param int $id
     * @return array
     */
    public static function whoIs($id=false) {
        $user = false;
        if ($id) {
            $user = Humble::entity('default/user/identification')->setId($id)->load();
        }
        return $user;
    }

    /**
     * Gets who I think I am...
     *
     * @return array
     */
    public static function whoAmI() {
        return (isset($_SESSION['uid']) ? $_SESSION['uid'] : null);
    }

   /**
     * Gets who I really am...
     *
     * @return array
     */

    public static function whoAmIReally() {
        return isset($_SESSION['login']) ? $_SESSION['login'] : (isset($_SESSION['uid']) ? $_SESSION['uid'] : null);
    }


    /**
     * Just a wrapper for shell execution, allows us to at some time add some command screening
     *
     * @param string $cmd
     * @return type
     */
    public static function command($cmd=false) {
        exec($cmd,$results,$rc);
        return $results;
    }

    public static function myIPAddress() {
        return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'Unknown';
    }

    /**
     * Returns the contents of the project file of false if the project hasn't been created yet, or possibly a node of the project file if the node name is passed and is present
     *
     * @return object
     */
    public static function getProject($node=false) {
        $project = (self::$project) ? self::$project : (self::$project = (file_exists('../Humble.project') ? json_decode(file_get_contents('../Humble.project')) : false));
        if ($node) {
            $project = (isset(self::$project->$node) ? self::$project->$node : null);
        }
        return $project;
    }

    /**
     * Just a relay for a shorter naming syntax
     * 
     * @param type $node
     * @return type
     */
    public static function project($node=false) {
        return self::getProject($node);
    }    
    
    /**
     * 4000th recursive thingy routine
     * 
     * @param type $struct
     * @param type $nodes
     * @return type
     */
    private static function recurse($struct=false,$nodes=false) {
        foreach ($nodes as $field => $node) {
            $app = isset($struct->$field) ? $struct->$field : false;
            $app = ($app && is_array($node)) ? self::recurse($app,$node) : (isset($app->$node) ? $app->$node : false);
        }
        return $app;
    }
    
    /**
     * Returns (or tries to at least) the value of a node from the etc/application.xml configuration file
     * 
     * @param type $node
     * @param type $dontUseCache
     * @return type
     */
    public static function application($node=false,$dontUseCache=false) {
        $app = self::loadApplicationMetaData($dontUseCache);
        if ($node) {
            $app = is_array($node) ? self::recurse($app,$node) : (isset($app->$node) ? $app->$node : null);
        }
        return $app;
    }

    /**
     *
     */
    public static function runningTasks() {
        $tasks = array();
    /*    exec('tasklist',$output);
        $tot = count($output);
        for ($i=3; $i<$tot; $i++) {
            $tasks[$pid = trim(substr($output[$i],26,8))] = array(
                "program" => trim(substr($output[$i],0,25)),
                "pid"   => $pid,
                "name"  => trim(substr($output[$i],35,16)),
                "session" => trim(substr($output[$i],52,11)),
                "memory" => trim(substr($output[$i],64))
             );
        }
        ksort($tasks);*/
        return $tasks;
    }

    /**
     * A check to see if the module identified by the namespace is enabled
     *
     * @param string $namespace
     * @return boolean
     */
    public static function isEnabled($namespace) {
        $info       = self::getModule($namespace);
        return ($info['enabled']=='Y');
    }

    /**
     * Check to see if a particular module is installed
     *
     * @param string $namespace
     * @return boolean
     */
    public static function isInstalled($namespace)  {
        $info       = self::getModule($namespace);
        return ($info !== null);
    }

    private static function findFilesInDirectory($root=false,$filename='') {
        $files  = [];
        $dir    = dir($root);
        while (($entry = $dir->read()) !== false) {
            if (($entry === '.') || ($entry === '..')) {
                continue;
            }
            $file = $root.'/'.$entry;
            if (is_dir($file)) {
                $files = array_merge($files,self::findFilesInDirectory($file,$filename));
            } else if ($entry === $filename) {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * Reads all configuration files in the project and returns those that are marked as "required", sorting by weight
     *
     * @return array
     */
    public static function getRequiredModuleConfigurations() {
        $available      = []; $required       = [];
        $all_configs    = self::findFilesInDirectory('Code','config.xml');
        foreach ($all_configs as $config) {
            if (strpos($config,'sample')!==false) {  continue;    }
            foreach ((new SimpleXMLElement(file_get_contents($config))) as $contents) {
                if (isset($contents->module->required) && ($contents->module->required == 'Y')) {
                    $weight             = (isset($contents->module->weight)) ? (int)$contents->module->weight : 99;
                    $available[$weight] =(!isset($available[$weight])) ? [] : $available[$weight];
                    $available[$weight][] = $config;
                }
            }
        }
        ksort($available);
        foreach ($available as $weight => $configs) {
            foreach ($configs as $config) {
                $required[] = $config;
            }
        }
        return $required;
    }

    /**
     * Packages are synonymous with directories under the 'Code' directory.  This method just returns the active packages
     *
     * @return array
     */
    public static function getAvailablePackages() {
        $pkgs   = [];
        $dir    = dir('Code');
        while (($entry = $dir->read()) !== false) {
            if (($entry === '.') || ($entry === '..')) {
                continue;
            }
            if (is_dir('Code/'.$entry)) {
                $pkgs[] = $entry;
            }
        }
        return $pkgs;
    }

    /**
     *
     */
    public static function getCompiler()  {
        return new \Code\Framework\Humble\Helpers\Compiler();
        //return Singleton::getCompiler();
    }

    /**
     *
     */
    public static function getMonitor()  {
        return Singleton::getMonitor();
    }
    
    /**
     *
     */
    public static function getInstaller()  {
        return Singleton::getInstaller();
    }

    /**
     *
     */
    public static function getUpdater()  {
        return Singleton::getUpdater();
    }

    /**
     *
     */
    public function __clone()        {        }
    public function __wakeup()       {        }
}
