<?php
    spl_autoload_register(['Humble_Autoloader_PSR_4_Compliant','handler']); //Our Autoloader
    require_once('vendor/autoload.php');                                    //Their Autoloader
    //###########################################################################
    
    if (!function_exists('underscoreToCamelCase')) {
        function underscoreToCamelCase($string, $first_char_caps=false) {
            return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
        }    
    }
    //----------------------------------------------------------------------------------------------------------------------
    //Autoloader, assumes you are in the 'app' directory.  If not, this will blow up like the reactor at Fukishima...
    //----------------------------------------------------------------------------------------------------------------------
    class Humble_Autoloader_PSR_4_Compliant {

        private static $constantsLoaded = false;
        private static $project         = false;
        
        public static function handler($className) {
               if (!self::$constantsLoaded) {
                require_once 'Environment.php';
                require_once 'Code/Framework/Humble/includes/Constants.php';
                self::$project          = \Environment::project();
                if ((self::$project->project_name !== 'Humble') && file_exists('Code/'.self::$project->package.'/'.self::$project->module.'/includes/Constants.php')) {
                    require_once 'Code/'.self::$project->package.'/'.self::$project->module.'/includes/Constants.php';            //Enumeration type stuff
                }                
                self::$constantsLoaded = true;
            }
            if ($className === 'Settings') {
                if (!file_exists('../../Settings/'.self::$project->namespace.'/Settings.php')) {
                    header('Location: /install.php');
                    die();
                }
                require_once('../../Settings/'.self::$project->namespace.'/Settings.php');
            } else {
                $classLocation = str_replace('\\',DIRECTORY_SEPARATOR,$className);
                if (file_exists($classLocation.".php")) {
                    require_once($classLocation.".php");
                } else {
                    throw new \Exceptions\PSR4ClassNotFoundException($className.' Not Found [PSR4]',12);
                }
            }
        }
    }
