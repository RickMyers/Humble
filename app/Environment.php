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
    public static function getClassName() {
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
     * Returns the location of the modules root, use this instead of hardcoding module paths
     *
     * @param type $namespace
     * @return boolean
     */
    public static function getRoot($namespace=false) {
        $root = false;
        if ($namespace) {
            $module = Humble::getModule($namespace);
            if ($module) {
                $name = (strpos($module['models'],"_")) ? explode('_',$module['models']) : explode('/',$module['models']);
                $root = 'Code/'.$module['package'].'/'.$name[0];
            }
        }
        return $root;
    }

    /**
     * Returns the namespace of the primary (first) module identified when the installation took place
     * 
     * @return string
     */
    public static function namespace() {
        if (!self::$project) {
           self::$project = self::getProject();
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
     * Returns if the Micro-Services Architecture Router Flag is set and activated
     * 
     * @return boolean
     */
    public static function MSARouter() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        return (isset(self::$application['msa']['router'])
                && ((self::$application['msa']['router']===1)
                || (self::$application['msa']['router']==='Y')));
    }

    /**
     * Returns the current State of the system
     * 
     * @return boolean
     */
    public static function state() {
        if (!self::$application) {
            self::loadApplicationMetaData();
        }
        $x = self::$application['state'];
        return isset(self::$application['state']) ?  self::$application['state'] : 'Unknown';
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
        return (isset(self::$application['state']) && (self::$application['state']==='PRODUCTION'));
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
        return (!isset(self::$application['state']) || (isset(self::$application['state']) && (self::$application['state']==='DEVELOPMENT')));
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
        return (isset(self::$application['state']) && (self::$application['state']==='TEST'));
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
        return (isset(self::$application['state']) && (self::$application['state']==='DEBUG'));
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
        return (isset(self::$application['state']) && (self::$application['state']==='DEBUG'));
    }
    

    
    /**
     * We are using Rain 3 for internal "in-line" templating
     *
     * @return \Rain\Tpl
     */
    public static function getInternalTemplater($root='',$extension='rain',$cache=false) {
        $root = ($root) ? $root : getcwd();
        $root = (substr($root,-1,1)==='/') ? $root : $root.'/';
        $cache = ($cache) ? $cache : $root.'cache/';
        $config = array(
            'tpl_dir'   => $root,
            'tpl_ext'   => $extension,
            'cache_dir' => $cache
        );
        \Rain\Tpl::configure($config);
        return new \Rain\Tpl;
    }

    public static function recacheApplication() {
        self::$application = json_decode(json_encode(simplexml_load_string((file_exists('../application.xml')) ? file_get_contents('../application.xml') : die("The application is inaccessible at this time."))),true);
        Humble::cache('application',self::$application);        
    }
    /**
     * Either returns the previously loaded metadata or loads and returns it
     * 
     * @return Array
     */
    public static function loadApplicationMetaData($dontUseCache=false) {
        if ($dontUseCache) {
            return json_decode(json_encode(simplexml_load_string((file_exists('../application.xml')) ? file_get_contents('../application.xml') : die("The application is inaccessible at this time."))),true);
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
     * Returns if caching is enabled.  We are making caching a hard requirement with the new enhancements to caching
     *
     * @return boolean
     */
    public static function cachingEnabled() {
       // if (!self::$application) {
       //     self::loadApplicationMetaData();
       // }
      //  return (isset(self::$application->status) && isset(self::$application->status->caching) && (int)self::$application->status->caching);
        return true;
    }
    
    public static function serialNumber() {
        if (!self::$project) {
           self::$project = self::getProject();
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
                if (isset(self::$application['status'])) {
                    if (isset(self::$application['status']['quiescing']) && ((int)self::$application['status']['quiescing'])) {
                        $status = "System is going offline...";
                    } else if (isset(self::$application['status']['enabled']) && ((int)self::$application['status']['enabled'])) {
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
        //Allows override if you are a super user
        /*if ($status) {
         * @TODO: Change this so when logged in, the user permissions are cached and we get this from the session
            if (isset($_SESSION['uid']) && $_SESSION['uid']) {
                $user = \Humble::getEntity('humble/user/permissions')->setId($_SESSION['uid']);
                $user->load();
                if ($user->getSuperUser() == 'Y') {
                    $status = false;
                }
            }
        }*/
        if ($status !== false) {
            header("location: /index.html?m=".$status);
            die();
        }
        return (isset(self::$application['status']['authorization']) && (int)self::$application['status']['authorization']['enabled']); //this will always, or should always, be false
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
            $user = Humble::getEntity('humble/user/identification')->setId($id)->load();
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
        exec($cmd,$results);
        return $results;
    }

    public static function myIPAddress() {
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * Returns the contents of the project file of false if the project hasn't been created yet, or possibly a node of the project file if the node name is passed and is present
     *
     * @return object
     */
    public static function getProject($node=false) {
        $project = (self::$project) ? self::$project : (self::$project =  (file_exists('../Humble.project') ? json_decode(file_get_contents('../Humble.project')) : false));
        if ($node) {
            $project = (isset(self::$project->$node) ? self::$project->$node : null);
        }
        return $project;
    }

    
    public static function getApplication($node=false,$dontUseCache=false) {
        $app = self::loadApplicationMetaData($dontUseCache);
        if ($node) {
            $app = (isset($app[$node]) ? $app[$node] : null);
        }
        return $app;
    }

    /**
     *
     */
    public static function runningTasks() {
        $tasks = array();
        exec('tasklist',$output);
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
        ksort($tasks);
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
        return new \Code\Base\Humble\Helpers\Compiler();
        //return Singleton::getCompiler();
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
