<?php
namespace Code\Framework\Humble\Models;

/**
 * Any ORM Engine (MySQL, Postgres, etc) must implement these methods
 */
interface ORMEngine {
    public function connect();
    public function query($query);
    public function buildWhereClause($useKeys);
  //  public function calculateStats();
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

    protected $_lastQuery   = null;
    protected $_lastError   = null;
    protected $_prefix      = null;
    protected $_namespace   = null;
    protected $_isVirtual   = false;  
    protected $_rows        = null;
    protected $_page        = null;
    protected $_fromRow     = null;
    protected $_toRow       = null;
    protected $_cursor      = null;
    protected $_unity       = null;
    protected $_orderBy     = [];
    protected $_orderBuilt  = false;
    protected $shortList    = [ 'Humble',
                                'Code\Framework\Humble\Helpers\Installer',
                                'Code\Framework\Humble\Helpers\Updater',
                                'Code\Framework\Humble\Helpers\Compiler'
                              ]; //These classes are allowed to specifically request a connection to the DB
    
    /**
     * Constructor
     */
    public function __construct() {
        //print("ORM Parent\n");
    }
    
    /**
     * This is a link back to the Unity object for daisy chaining methods
     * 
     * @param object $caller
     */
    public function linkUnity($unity=false) {
        if ($unity) {
            $this->_unity = $unity;
        }
    }
    
    protected function underscoreToCamelCase($string, $first_char_caps=false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }    
    
    /**
     *
     */
    public function _pages()            {
        $pages = 1;
        if ($this->_rows() && $this->_rowCount) {
            $pages = ceil($this->_rowCount/$this->_rows());
        }
        return $pages;
    }

    /**
     *
     */
    public function _page($arg=false)   {
        if ($arg === false) {
            return $this->_page;
        } else {
            $this->_page = ($arg > 1) ? $arg : 1;
        }
        return $this->_unity;
    }

    /**
     *
     */
    public function _rows($arg=false) {
        if ($arg === false) {
            return $this->_rows;
        } else {
            $this->_rows = $arg;
        }
        return $this->_unity;
    }
    
    /**
     *
     */
    public function _rowCount($arg=false){
        if ($arg === false) {
            return $this->_rowCount;
        } else {
            $this->_rowCount  = $arg;
        }
        return $this->_unity;
    }

    /**
     *
     */
    public function _fromRow($arg=false) {
        if ($arg === false) {
            return $this->_fromRow;
        } else {
            $this->_fromRow  = $arg;
        }
        return $this->_unity;
    }

    /**
     * 
     * @param type $arg
     * @return $this
     */
    public function _toRow($arg=false) {
        if ($arg === false) {
            return $this->_toRow;
        } else {
            $this->_toRow   = $arg;
        }
        return $this->_unity;
    }    
    /**
     * 
     * @param type $arg
     * @return mixed
     */
    public function _rowsReturned($arg=false) {
        if ($arg === false) {
            return $this->_rowsReturned;
        } else {
            $this->_rowsReturned    = $arg;
        }
        return $this->_unity;
    }
    
    /**
     *
     */
    public function _currentPage($arg=false) {
        if ($arg === false) {
            return $this->_currentPage;
        } else {
            $this->_currentPage                = $arg;
        }
        return $this->_unity;    
    }
    
    /**
     * For pagination, can set whether to use a cursor or not.  Primarily for use when in a controller
     * 
     * @param bool $cursor
     * @return $this
     */
    public function _cursor($cursor=null) {
        if ($cursor!==null) {
            $this->_cursor = $cursor;
            return $this->_unity;
        }
        return $this->_cursor;
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

    /**
     * Returns the last query executed, or saves it off if passed in
     *
     * @param string $qry
     * @return String
     */
    public function _lastQuery($qry=false) {
        if ($qry===false) {
            return $this->_lastQuery;
        } else {
            $this->_lastQuery = $qry;
        }
        return $this->_unity;
    }

    /**
     * Gets the last error encountered, or sets the last error encountered
     *
     * @param type $err
     * @return type
     */
    public function _lastError($err=false) {
        if ($err===false) {
            return $this->_lastError;
        } else {
            $this->_lastError = $err;
        }
        return $this->_unity;
    }
    

}