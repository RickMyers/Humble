<?php
    spl_autoload_register(['Humble_Autoloader_PSR_4_Compliant','handler']); //Our Autoloader
    require_once('vendor/autoload.php');                                        //Their Autoloader

    //----------------------------------------------------------------------------------------------------------------------
    //Autoloader, assumes you are in the 'app' directory.  If not, this will blow up like the reactor at Fukishima...
    //----------------------------------------------------------------------------------------------------------------------
    class Humble_Autoloader_PSR_4_Compliant {

        public static function handler($className) {
            if ($className === 'Settings') {
                $project = \Environment::getProject();
                if (!file_exists('../../Settings/'.$project->namespace.'/Settings.php')) {
                    header('Location: /install.php');
                    die();
                }
                require_once('../../Settings/'.$project->namespace.'/Settings.php');
            } else {
                $classLocation = str_replace('\\',DIRECTORY_SEPARATOR,$className);
                if (file_exists($classLocation.".php")) {
                    require_once($classLocation.".php");
                } else {
                    print("\n$className".' Not Found [PSR4]'."\n");
                }
            }
        }
    }
