<?php
namespace Code\Framework\Humble\Drivers;

/**
 * Any ORM Engine (MySQL, Postgres, etc) must implement these methods
 */
interface ORMEngine {
    public function connect();
    public function query($query);
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
    private   $shortList    = [ 'Humble',
                                'Code\Framework\Humble\Helpers\Installer',
                                'Code\Framework\Humble\Helpers\Updater',
                                'Code\Framework\Humble\Helpers\Compiler'
                              ]; //These classes are allowed to specifically request a connection to the DB
    
    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     *
     */
    public function _isVirtual($state=null) {
        if ($state === null) {
            return $this->_isVirtual;
        } else {
            $this->_isVirtual = $state;
        }
        return $this;
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
     * Enables or disables query logging
     * 
     * @return type
     */
    public function logging() {
       return \Humble::cache('queryLogging',$this->getStatus() ? ($this->getStatus()==='On') : false);
    }

    /**
     * Returns the last query executed, or saves it off if passed in
     *
     * @param string $qry
     * @return String
     */
    public function _lastQuery($qry=false) {
        if ($qry) {
            $this->_lastQuery = $qry;
        }
        return $this->_lastQuery;
    }

    /**
     * Gets the last error encountered, or sets the last error encountered
     *
     * @param type $err
     * @return type
     */
    public function _lastError($err=false) {
        if ($err) {
            $this->_lastError = $err;
        }
        return $this->_lastError;
    }

    public function _prefix($prefix=false) {
        if ($prefix) {
            $this->_prefix = $prefix;
        } else {
            return $this->_prefix;
        }
        return $this;
    }

    public function _namespace($ns=false) {
        if ($ns) {
            $this->_namespace = $ns;
        } else {
            return $this->_namespace;
        }
        return $this;
    }
    
    
}