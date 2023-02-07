<?php
namespace Code\Base\Humble\Models;
/**    
 *
 * An iterator for managing result sets
 *
 * The results of an SQL query is injected into this iterator with the
 * purpose of suppressing certain unnecessary fields, like the polyglot
 * _id from Mongo
 *
 * PHP version 7.2+
 *
 * @category   .then(
 * @package    Framework
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-.then(_Model_Iterator.html
 * @since      File available since Version 1.0.1
 */
class Iterator extends Model implements \Iterator, \Countable
{

    private   $position   = 0;
    protected $array      = [];
    private   $clean      = true;
    private   $translate  = false;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->position = 0;
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
     * Sets the array to iterate over and remove any MongoDB references if clean has been turned on
     *
     * @param type $array
     * @return \.then(_Model_Iterator
     */
    public function set($array=[]) {
        $this->array = $array;
        if ($this->clean) {
            foreach ($this->array as $idx => $row) {
                if (isset($this->array[$idx]['_id'])) {
                    unset($this->array[$idx]['_id']);
                }
            }
        }
        return $this;
    }

    /**
     * Back to the beginning of the array
     */
    public function rewind() : void {
        $this->position = 0;
    }

    /**
     * Returns the current element of the array, and if translation is requested, then each field is returned as a custom Humble string object
     *
     * @return  
     */
    #[\ReturnTypeWillChange]
    public function current() {
        $record = null;
        if ($this->translate) {
            if (is_array($this->array[$this->position])) {
                foreach ($this->array[$this->position] as $key => $val) {
                    $record[$key] = \Humble::string($val);
                }
            } else {
                $record = \Humble::string($this->array[$this->position]);
            }
        } else {
            $record = $this->array[$this->position];
        }
        return $record;
    }
    
    /**
     * 
     * @return 
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return $this->position;
    }

    public function next() : void {
        ++$this->position;
    }

    public function valid() : bool {
        return isset($this->array[$this->position]);
    }

    public function toArray() {
        return $this->array;
    }

    public function __toString() {
        return json_encode($this->array);
    }

    public function count($mode=0) : int {
        return count($this->array,$mode);
    }

    public function first() {
        return (isset($this->array[0]) ? $this->array[0] : []);
    }

    public function pop() {
        return array_pop($this->array);
    }

    public function push($what) {
        $this->array[] = $what;
        return $this;
    }

    /**
     * Converts the two dimensional array of fields to a simple one dimensional array consisting of the first row of fields
     */
    public function snip() {
        $this->array =  (isset($this->array[0])) ? $this->array[0] : []; 
        return $this;
    }
    
    public function withTranslation($arg=false) {
        $this->translate = $arg;
        return $this;
    }
}