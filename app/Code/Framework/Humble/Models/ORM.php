<?php
namespace Code\Framework\Humble\Models;

/**
 * Any ORM Engine (MySQL, Postgres, etc) must implement these methods
 */
interface ORMEngine {
    public function connect();
    public function query($query);
    public function buildWhereClause($useKeys);
    public function calculateStats($noLimitQuery,&$results);
    public function buildOrderByClause();
    public function addLimit($page);
    public function close();
}

/**
 *
 * DB Engine Parent
 *
 * Parent class for all supported DB engines
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class ORM
{

    public    $_unity       = null;
    protected $shortList    = [ 'Humble',
                                'Code\Framework\Humble\Helpers\Installer',
                                'Code\Framework\Humble\Helpers\Updater',
                                'Code\Framework\Humble\Helpers\Compiler'
                              ]; //These classes are allowed to specifically request a connection to the DB
    
    /**
     * Constructor
     */
    public function __construct() {
        //nop
    }
    
    /**
     * This is a link back to the Unity object for daisy chaining methods
     * 
     * @param object $caller
     */
    public function linkUnity($unity=false) {
        print("Trying to link\n");
        if ($unity) {
            $this->_unity = $unity;
            print("Linked\n");
        }
        return $this;
    }
    
    protected function underscoreToCamelCase($string, $first_char_caps=false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }    
    

    /**
     *
     * @param type $rs
     * @return type
     */
    protected function translateResultSet($rs=null)	{
	$n_rs = [];
	if (($rs) && $rs->num_rows > 0) {
            while ($row = $rs->fetch_assoc()) {
                $n_rs[] = $row;
            }
        }
	return $n_rs;
    }

}