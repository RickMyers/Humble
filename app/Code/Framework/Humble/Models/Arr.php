<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * An Array Extension
 *
 * We are going to extend the base ArrayObject in PHP and add some needed
 * functionality
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2005-present Humble
 * @license    https://humbleprogramming.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://humbleprogramming.com/docs/class-Array.html
 * @since      File available since Release 1.0.0
 */
class Arr extends \ArrayObject
{

    protected           $_prefix        = null;
    protected           $_namespace     = null;

    public function __construct($arr=[]) {
        parent::__construct($arr);
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
     * Used to resolve if this array is empty.  Normally, a true empty array would resolve to false, but not when a class like this, even if there's no elements in the array
     * 
     * @return boolean
     */
    public function isEmpty() {
        return (count($this->getArrayCopy()) === 0);
    }
    
    
    /**
     * Just here to comply with the framework requirements
     */
    public function _isVirtual($state=null) {
         return false;
    }
    
    /**
     * Can set or get namespace being used by the current class.
     *
     * If you pass in a value, it stores that value as the namespace, otherwise it
     * returns what ever value is currently stored as the namespace
     *
     * @param timestamp $arg A namespace to use
     * @return string The current namespace
     */
    public function _namespace($arg=false) {
        if ($arg) {
            $this->_namespace = $arg;
            return $this;
        } else {
            return $this->_namespace;
        }
    }

    /**
     * Can set or get db prefix being used by the current namespace
     *
     * If you pass in a value, it stores that value as the DB prefix, otherwise it
     * returns what ever value is currently stored as the DB prefix
     *
     * @param string $arg A prefix to use
     * @return string The current DB prefix
     */
    public function _prefix($arg=false) {
        if ($arg) {
            $this->_prefix = $arg;
        } else {
            return $this->_prefix;
        }
        return $this;
    }    

    /**
     * Override the tostring method to return the array as JSON...
     * 
     * @return type
     */
    public function __toString() {
        return json_encode($this->getArrayCopy());
    }

    /**
     * Specifically calls the parent constructor to create the array
     * 
     * @param type $arr
     * @return $this
     */
    public function set($arr) {
        parent::__construct($arr);
        return $this;
    }
    
    /**
     * Handy dandy method handle for array manipulating functions
     * 
     * @param type $func
     * @param type $argv
     * @return type
     * @throws BadMethodCallException
     */
    public function __call($func, $argv) {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_') {
            throw new \BadMethodCallException(__CLASS__.'->'.$func);
        }
        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }    

}