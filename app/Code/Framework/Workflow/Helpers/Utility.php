<?php
namespace Code\Framework\Workflow\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * General Utility Methods
 *
 * General Purpose Utility Methods
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Paradigm Engine
 * @author     rmyers rick@humbleprogramming.com
 */
class Utility extends Helper
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

    public function fetchWorkflowElementCode() {
        $code       = '';
        $namespace  = $this->getNamespace();
        $class      = ucfirst($this->getComponent());
        $method     = $this->getMethod();
        if ($module    = Humble::module($namespace)) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['models'].'/'.$class.'.php')) {
                $code  = ''; 
                $found = $ctr = 0;
                $this->setTotalLines($tot   = count($lines = explode("\n",$code .= file_get_contents($file))));
                while (!$found && ($ctr<$tot)) {
                    if (!$found = strpos($lines[$ctr],'function '.$method.'(')) {
                        $ctr++;
                    }
                }
            }
            $this->setMethodLineNumber($ctr);
        }
        return $code;
    }
}