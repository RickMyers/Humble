<?php
namespace Code\Framework\Humble\Models;
/**    
 *
 * An iterator for managing query results that are simple arrays
 *
 * The results of an SQL query is injected into this iterator with the
 * purpose of being consistent with the default Iterator class
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/
 * @since      File available since Version 1.0.1
 */
class SimpleIterator extends Model implements \Iterator, \Countable
{

    private $position   = 0;
    private $array      = [];
    private $clean      = false;
    private $translate  = false;

    /**
     * Constructor
     */
    public function __construct($array=false) {
        parent::__construct();
        $this->position = 0;
        if ($array && is_array($array)) {
            $this->set($array);
        }
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return class name
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * A flag for whether to remove mongoDB references from the result set
     *
     * @param type $arg
     */
    public function clean($arg=true) {
        $this->clean = $arg;
        return $this;
    }

    /**
     *  Sets the array to iterate over and remove any MongoDB references if clean has been turned on
     * 
     * @param type $array
     * @return $this
     */
    public function set($array=[]) {
        if ($this->clean) {
            if (isset($array['_id'])) {
                unset($array['_id']);
            }            
        }
        foreach ($array as $key => $val) {
            $this->array[] = [$key => $val];
        }
        return $this;
    }

    public function get() {
        $a = [];
        foreach ($this->array as $field) {
            foreach ($field as $key => $val) {
                $a[$key] = $val;
            }
        }
        return $a;
    }
    
    /**
     * Back to the beginning of the array
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Returns the current element of the array, and if translation is requested, then each field is returned as a custom Humble string object
     *
     * @return type
     */
    public function current() {
        $record = null;
        if ($this->translate) {
            foreach ($this->array[$this->position] as $key => $val) {
                $record[$key] = \Humble::string($val);
            }
        } else {
            $record = $this->array[$this->position];
        }
        return $record;
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->array[$this->position]);
    }

    public function toArray() {
        return $this->get();
    }

    public function __toString() {
        return json_encode($this->get());
    }

    public function count($mode=0) {
        return count($this->array,$mode);
    }

    public function fetch($position=false) {
        $retval = null;
        $position = ($position===false) ? $this->position : $position;
        if (isset($this->array[$position])) {
            foreach (($field = $this->array[$position]) as $key=>$val) {
                $retval=[
                    $key => $val
                ];
            }
        }
        return $retval;        
    }
    
    public function first() {
        $this->position = 0;
        return $this->fetch();
    }

    public function pop() {
        $field = array_pop($this->array);
        foreach ($field as $var => $val) {
            $field = [$var => $val];
        }
        return $field;
    }

    public function push($what) {
        $this->array[] = $what;
        return $this;
    }

    public function withTranslation($arg=false) {
        $this->translate = $arg;
        return $this;
    }
}
