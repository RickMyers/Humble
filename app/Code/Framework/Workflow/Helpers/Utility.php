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

    /**
     * Attempts to stick a stub in an existing class
     */
    public function workflowElementNewMethod() {
        $namespace  = $this->getNamespace();
        $class      = ucfirst($this->getComponent());
        $method     = $this->getMethod();
        $type       = $this->getType();
        $newcode    = '';
        if ($module    = Humble::module($namespace)) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['models'].'/'.$class.'.php')) {
                $cnt    = $lines  = trim(file_get_contents($file));
                $stub   = str_replace(['&&METHOD&&'],[$method],file_get_contents('Code/Framework/Paradigm/lib/templates/'.$type.'.tpl'));
                $x      = strlen($lines)-1;
                while ($x && ($lines[$x] != '}')) {
                    $x = $x - 1;
                }
                $newcode = substr($lines,0,$x).$stub.'}';
            }
        }
        return htmlentities($newcode);
    }
    
    /**
     * Tries to find the code and return it along with the position in the file
     * 
     * @return string
     */
    public function fetchWorkflowElementCode() {
        $code       = '';
        $namespace  = $this->getNamespace();
        $class      = ucfirst($this->getComponent());
        $method     = $this->getMethod();
        if ($module    = Humble::module($namespace)) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['models'].'/'.$class.'.php')) {
                $this->setSourceFile($file);
                $code  = ''; 
                $found = $ctr = 0;
                $this->setTotalLines($tot   = count($lines = explode("\n",$code .= file_get_contents($file))));
                while (!$found && ($ctr<$tot)) {
                    if (!$found = strpos($lines[$ctr],'function '.$method.'(')) {
                        $ctr++;
                    }
                }
                if ($found) {
                    $this->setScrollAmount(round($ctr/$tot,2));
                }
            }
            $this->setMethodLineNumber($ctr+1);
        }
        return htmlentities($code);
    }
}