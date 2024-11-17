<?php
namespace Code\Framework\Paradigm\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * JavaScript Editor Helper Methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Paradigm Engine
 * @author     rmyers rick@humbleprogramming.com
 */
class Editor extends Helper
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
     * Lists all files in a resource directory
     * 
     * @return json
     */
    public function listResources() {
        $resources = [];
        if ($module = Humble::module($this->getNamespace())) {
            if ($module['resources_js']) {
                if ($dh = dir('Code/'.$module['package'].'/'.$module['resources_js'])) {
                    while (($entry = $dh->read())!==false) {
                        if (($entry == '.') || ($entry == '..')) {
                            continue;
                        }
                        $resources[] = $entry;
                    }
                }
            }
        }
        return $resources;
    }
    
    /**
     * Returns a specific module's resource code or the default on if the resource isn't found
     * 
     * @return string
     */
    public function editResource() {
        $result = '';
        if ($module = Humble::module($this->getNamespace())) {
            $file = 'Code/'.$module['package'].'/'.$module['resources_js'].'/'.$this->getResource();
            if (!file_exists($file)) {
                @copy('Code/Framework/Paradigm/Resources/templates/jsadapter.js',$file);
            }
            $result = file_get_contents($file);
        }
        return $result;
    }
    
    /**
     * Will save the edited code off to the JS resources folder
     */
    public function saveResource() {
        if ($module = Humble::module($this->getNamespace())) {
            $file = 'Code/'.$module['package'].'/'.$module['resources_js'].'/'.$this->getResource();        
            if ($code = $this->getCode()) {
                file_put_contents($file,$code);
            }
        }
    }
}