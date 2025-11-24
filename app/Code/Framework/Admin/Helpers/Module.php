<?php
namespace Code\Framework\Admin\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * Module/Admin related methods
 *
 * Utility methods for Modules
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Core
 * @author     Rick Myers rick@humbleprogramming.com
 */
class Module extends Helper
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Takes an uploaded module in a zip file and figures out where to install it.  Great for hotfixes
     * 
     * @param string $module
     */
    public function upload($module=false) {
        $message = "Error: The module was NOT uploaded";
        $package = $this->getPackage();
        if ($package && ($module = ($module) ? $module : ($this->getModule() ? $this->getModule() : false))) {
            $zip    = new \ZipArchive();
            $zip->open($module['path']);                  
            $data   = [];                                                       //We are going to get module name from the first file in the zipped folder
            for ($i=0; $i<$zip->numFiles; $i++) {
                if ($data = $zip->statIndex($i)) {
                    break;
                }
            }
            $name   = (explode('/',$data['name']))[0];                          //First part of the filename will be the module name
            $xml    = $zip->getFromName($name.'/etc/config.xml');               //We are going to need to read the configuration file to determine where to install the module
            $struct = simplexml_load_string($xml);
            foreach ($struct as $namespace => $nodes) {
                break;                                                          //get the first node of the XML structure which is the namespace
            }
            //$package = (string)$struct->$namespace->module->package;            //Now we can find the package (directory) name to install the module in.
            $zip->extractTo('Code/'.$package);                                  //And now we unzip the module there
            $struct->$namespace->module->package = $package;                    //Assign the correct package (directory) name
            file_put_contents('Code/'.$package.'/'.$name.'/etc/config.xml',$struct->asXML());
            $message = 'The Module was uploaded, you may now run the install step';
/*            if (!$existing_module) {
                print(shell_exec(Environment::PHPLocation().' CLI.php --u ns='.$namespace));
            } 
            print(shell_exec(Environment::PHPLocation().' CLI.php --u ns='.$namespace));*/
            
        }
        return $message;
    }

}
