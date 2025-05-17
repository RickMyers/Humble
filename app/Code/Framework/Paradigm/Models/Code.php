<?php
namespace Code\Framework\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Paradigm Engine Code Manager
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Paradigm Engine
 * @author     Rick <rick@humbleprogramming.com>
 */
class Code extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;
	
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
     * Saves a model after update
     * 
     * @return string
     */
    public function save() {
        $message    = 'Source File Error, not saved';
        $class      = $this->getClass();
        $namespace  = $this->getNamespace();
        $code       = $this->getCode();
        if ($namespace && ($module = Humble::module($namespace))) {
            $file = 'Code/'.$module['package'].'/'.$module['models'].'/'.$class.'.php';
            if (file_exists($file)) {
                $message = file_put_contents($file,$code) ? 'Saved' : 'Likely Permissions Error';
            }
        }
        return $message;
    }
}