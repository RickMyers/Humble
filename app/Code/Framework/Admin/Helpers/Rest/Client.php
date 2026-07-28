<?php
namespace Code\Framework\Admin\Helpers\Rest;
use Humble;
use Log;
use Environment;
/**
 *
 * Rest Client functions
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Other
 * @author     Rick rick@humbleprogramming.com
 */
class Client extends \Code\Framework\Admin\Helpers\Helper
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
    public function className() {
        return __CLASS__;
    }

    /**
     * Returns a list of controllers 
     * 
     * @param type $namespace
     * @return type
     */
    public function listControllers($namespace=false) {
        $controllers = [];
        if ($module = Humble::module(($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)))) {
            $dir = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']);
            $dh  = dir($dir);
            while ($entry = $dh->read()) {
                if (($entry == '.') || ($entry == '..') || ($entry === 'Cache')) {
                    continue;
                }
                if (strpos($entry,'.xml')) {
                    $controllers[] = $entry;
                }
            }
        }
        return $controllers;
    }
    
    public function listActions($namespace=false,$controller=false) {
        $actions = [];
        if ($controller = ($controller) ? $controller : ($this->getController() ? $this->getController() : false)) {
            if ($module = Humble::module(($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)))) {
                if (file_exists($file = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$controller)) {
                    $xml = simplexml_load_file($file);
                    foreach ($xml[0]->actions as $list) {
                        foreach ($list as $action) {
                            $attr = $action->attributes();
                            $actions[] = [ 
                                'name' => (string)$attr->name,
                                'description' => (string)$action->description
                            ];
                        }
                    }
                }
            }
        }
        return $actions;
    }
    
    private function extractParameters($action=[]) {
        $x = $action;
        return [];
    }
    
    public function listParameters($namespace=false,$controller=false,$action=false) {
        $parameters = [];
        if ($action = ($action) ? $action : ($this->getAction() ? $this->getAction() : false)) {
            if ($controller = ($controller) ? $controller : ($this->getController() ? $this->getController() : false)) {
                if ($module = Humble::module(($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)))) {
                    if (file_exists($file = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$controller)) {
                        $xml = simplexml_load_file($file);
                        foreach ($xml[0]->actions as $list) {
                            foreach ($list as $controller_action) {
                                $attr = $controller_action->attributes();
                                if ((string)$attr->name == $action) {
                                    $parameters = $this->extractParameters($controller_action);
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $parameters;
    }
}