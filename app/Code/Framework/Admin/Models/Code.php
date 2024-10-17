<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Code presentation and editing methods
 *
 * See title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Code extends Model
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
     * Tries to figure out where the source code is by resource and type
     * 
     * @param type $namespace
     * @param type $type
     * @param type $resource
     * @return string
     */
    protected function locateCode($type=false,$resource=false) {
        $location = '';
        if ($type && $resource) {
            $parts      = explode('/',$resource);
            $parts[0]   = ($parts[0]) ? $parts[0] : \Environment::namespace();
            $module     = Humble::module($parts[0]);            
            switch ($type) {
                case 'Model' :
                    $location = 'Code/'.$module['package'].'/'.$module['models'].'/'.ucfirst($parts[1]).'.php';
                    break;
                case 'Entity' :
                    $location = 'Code/'.$module['package'].'/'.$module['entities'].'/'.ucfirst($parts[1]).'.php';
                    break;
                case 'Helper' :
                    $location= 'Code/'.$module['package'].'/'.$module['helpers'].'/'.ucfirst($parts[1]).'.php';
                    break;
                case 'Collection' : 
                    break;
                default :
                    break;
            }
        }
        return $location;
    }
    
    /**
     * Finds the source code (if exists) and then print it out
     * 
     * @param type $namespace
     * @param type $type
     * @param type $resource
     * @return string
     */
    public function fetchSourceCode($type=false,$resource=false) {
        $type       = ($type)       ? $type      : $this->getType();
        $resource   = $resource     ? $resource  : $this->getResource();
        $source     = $this->locateCode($type,$resource);
        return file_exists($source) ? file_get_contents($source) : "\n\nNo Source (likely virtual class)\n\n";
    }

    /**
     * Saves the edited source code
     * 
     * @param type $namespace
     * @param type $type
     * @param type $resource
     */
    public function saveSourceCode($type=false,$resource=false) {
        $result     = 'Target file not found to overwrite';
        $type       = ($type)       ? $type      : $this->getType();
        $resource   = $resource     ? $resource  : $this->getResource();
        $source     = $this->locateCode($type,$resource);
        if (file_exists($source)) {
            $result = file_put_contents($source,$this->getSourceCode()) ? 'Saved' : 'Failed to save';
        }
        return $result;
    }
    
}