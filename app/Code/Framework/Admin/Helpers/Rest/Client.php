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
    
    /**
     * Generates just the list of actions, no additional nodes, from a controller
     * 
     * @param type $namespace
     * @param type $controller
     * @return type
     */
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

    /**
     * This array represents all the possible attributes available to a parameter statement, including on the "passalong" attribute
     * 
     * @return array
     */
    private function parameterRow() {
        return [
            'name'      => '',
            'user_value' => '',
            'required'  => false,
            'default'   => '',
            'value'     => '',
            'format'    => '',
            'encrypt'   => false,
            'decrypt'   => false,
            'source'    => 'request',
            'optional'  => false,
            'type'      => '',
            'range'     => false,
            'min'       => false,
            'max'       => false,
            'encode'    => false,
            'decode'    => false,
            'escape'    => false,
            'unescape'  => false,
            'upper'     => false,
            'lower'     => false
        ];
    }
    
    /**
     * Handles the parameters defined by the passalong attribute
     * 
     * @param type $passalong
     */
    private function processPassalongParameters($passalong=false) {
        $parameters = [];
        foreach (explode(',',$passalong) as $parm) {
            $var = '';
            $row = $this->parameterRow();
            foreach (explode(':',$parm) as $idx => $opt) {
                if ($idx === 0) {
                    $var = $row['name'] = $opt;
                    continue;
                }
                $parts = explode('=',$opt);
                if (isset($parts[1])) {
                    $row[$parts[0]] = $parts[1];
                }
            }
            $parameters[$var] = $row;
        }
        return $parameters;
    }
    
    /**
     * Casts a SimpleXML element into a printable form
     * @param type $val
     * @return type
     */
    private function translateXMLValue($val) {
        $t = strtoupper((string)$val);
        switch ($t) {
            case 'TRUE' : 
                $val = true;
                break;
            case 'FALSE' : 
                $val = false;
            default :
                if (is_int($val)) {
                    $val = (int)$val;
                } else if (is_float($val)) {
                    $val = (float)$val;
                } else {
                    $val = (string)$val;
                }
        }
        return $val;
    }
    
    /**
     * Just a little recursion between friends...
     * 
     * @param type $nodes
     * @return type
     */
    private function iterateNodes($nodes=[]) {
        $parm_list = [];
        foreach ($nodes as $node => $children) {
            if ($children->count()) {
                $parm_list = array_merge($parm_list,$this->iterateNodes($children));
            }
            if ($node == 'parameter') {
                $attr = $children->attributes();
                $name = (string)$attr->name;
                $parm_list[$name] = $this->parameterRow();
                foreach ($attr as $var => $val) {
                    if (isset($parm_list[$name][$var])) {
                        $parm_list[$name][$var] = $this->translateXMLValue($val);
                    }
                }
            }
        }
        return $parm_list;
    }
    
    /**
     * Gets the parameters list from the "passalong" attributed combined with any "parameter" nodes
     * 
     * @param type $action
     * @return array
     */
    private function extractParameters($action=[]) {
        $parameters = [];
        $attr       = $action->attributes();
        if (isset($attr->passalong)) {
            $parameters = array_merge($parameters,$this->processPassalongParameters((string)$attr->passalong));
        }
        return array_merge($parameters,$this->iterateNodes($action));
    }
    
    /**
     * Scans a particular controller action for parameters and returns any associated properties of those parameters
     * 
     * @param string $namespace
     * @param string $controller
     * @param string $action
     * @return array
     */
    public function listParameters($namespace=false,$controller=false,$action=false) {
        $parameters = [];
        if ($action = ($action) ? $action : ($this->getAction() ? $this->getAction() : false)) {
            if ($controller = ($controller) ? $controller : ($this->getController() ? $this->getController() : false)) {
                if ($module = Humble::module(($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)))) {
                    if (file_exists($file = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$controller)) {
                        $xml = simplexml_load_file($file);
                        foreach ($xml[0]->actions as $list) {
                            foreach ($list as $controller_action) {
                                if ((string)$controller_action->attributes()->name == $action) {
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