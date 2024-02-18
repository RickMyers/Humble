<?php
namespace Code\Framework\Humble\Models;
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
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
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
     * Because of the variable number of columns per row in a polyglot entity, this method "normalizes" the result set, which means all rows will have the same number of columns
     * 
     * @param iterator $iterator (optional
     * @return iterator
     */
    public function normalize() {
        $columns    = [];
        foreach ($this->array as $row) {
            foreach ($row as $column => $value) {
                $columns[$column] = $column;
            }
        }
        foreach ($this->array as $index => $row) {
            $newRow = [];
            foreach ($columns as $column) {
                $newRow[$column] = $row[$column] ?: '';
            }
            $this->array[$index] = $newRow;
        }
        return $this;
    }
    
    /**
     * Takes the current result set and returns it as 
     * 
     * @param type $data
     * @param type $delimiter
     * @param type $enclosure
     * @return string
     */
    public function toCSV($data, $delimiter = ',', $enclosure = '"',$value_headers=false) {
        $handle = fopen('php://temp', 'r+');
        $headers=[]; $contents = '';
        foreach ($data as $idx => $line) {
            if (!$headers) {
                foreach ($line as $field => $value) {   
                    $headers[] = trim(preg_replace("/[^A-Za-z0-9 _]/", '', ($value_headers ? $value : $field)));
                }
                if (!$value_headers) {
                    fputcsv($handle, $headers, $delimiter, $enclosure);
                }
            }            
            fputcsv($handle, $line, $delimiter, $enclosure);
        }
        rewind($handle);
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
    }    

    /**
     * Sets the array to iterate over and remove any MongoDB references if clean has been turned on
     * 
     * @param type $array
     * @return $this
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