<?php
namespace Code\Framework\Humble\Helpers;
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
     * Recursively add every file in the module to the archive
     * 
     * @param type $zip
     * @param type $dir
     * @return type
     */
    private static function zipDirectory($zip,$dir) {
        $dh = dir($dir);
        while ($entry = $dh->read()) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            (is_dir($dir.'/'.$entry)) ? self::zipDirectory($zip,$dir.'/'.$entry) : $zip->addFile($dir.'/'.$entry);
        }
        $dh->close();
        return $zip;
    }
    
    /**
     * Backup the existing module using a timestamp
     * 
     * @param type $name
     * @param type $path
     */
    protected function saveCurrent($name=false,$path=false) {
        @mkdir('tmp/Backup/Modules',0775,true);
        $archive = 'tmp/Backup/Modules/'.$name.'_'.date('YmdHis').'.zip';
        file_put_contents('tmp/Backup/.gitignore','*');
        $zip    = new \ZipArchive();
        $zip->open($archive,\ZipArchive::CREATE);
        $zip = self::zipDirectory($zip,$path);
        $zip->close();
    }
    
    protected function restoreCurrent() {
        
    }
    
    /**
     * Takes an uploaded module in a zip file and figures out where to install it.  Great for hotfixes
     * 
     * @param string $module
     */
    public function install($module=false) {
        if ($module = ($module) ? $module : ($this->getModule() ? $this->getModule() : false)) {
            $zip    = new \ZipArchive();
            $zip->open($module['path']);                                        //Standard humble file handling
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
            $package = (string)$struct->$namespace->module->package;            //Now we can find the package (directory) name to install the module in.
            $existing_module = file_exists('Code/'.$package.'/'.$name.'/etc/config.xml');
            self::saveCurrent($name,'Code/'.$package.'/'.$name);
            $zip->extractTo('Code/'.$package);                                  //And now we unzip the module there
            if (!$existing_module) {
                print(shell_exec(Environment::PHPLocation().' CLI.php --u ns='.$namespace));
            } 
            print(shell_exec(Environment::PHPLocation().' CLI.php --u ns='.$namespace));
            
        }
    }

}