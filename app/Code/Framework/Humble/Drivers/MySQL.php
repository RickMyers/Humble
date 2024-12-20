<?php
namespace Code\Framework\Humble\Drivers;
/**
 * MySQL connection manager
 *
 * Manages the connection to MySQL and reports on errors
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @version    1.0
 */
class MySQL extends ORM implements ORMEngine  {

    private $_dbref         = NULL;
    private $_state         = NULL;
    private $_prep          = NULL;
    private $_connected     = true;
    private $_environment   = null;
    private $_rowsAffected  = 0;

    /**
     * Loads the environment information, such as userid and password
     */
    public function __construct() {
        $this->_environment = \Singleton::getEnvironment();
        $this->connect();
    }
     
    /**
     * Connects to a DB source
     */
    public function connect() {
        global $USE_CONNECTION_POOL;                                            //use persistent connections?
        if (!$this->_dbref	=  @new \mysqli((($USE_CONNECTION_POOL) ? 'p:' : '').$this->_environment->getDBHost(),$this->_environment->getUserid(),$this->_environment->getPassword())) {
            die('Error attempting to connect to the database.  Is the server running?  If so, check you DB settings'."\n");
        }
        if ($this->_dbref->connect_error ?? false) {
            $errorstring="<error date=\"".date(DATE_RFC822)."\">\n";
            $errorstring .= "\t<class> ".$this->_environment->getDBHost()." </class>\n";
            $errorstring .= "\t<errorcode> Connection Error </errorcode>\n";
            $errorstring .= "\t<errortext> ".$this->_dbref->connect_error." </errortext>\n";
            $errorstring .= "</error>\n";
            \Log::sql($errorstring);
            $this->_connected = false;
            if (php_sapi_name()=='cli') {
                print($this->_dbref->connect_error."\n");
                debug_print_backtrace();
                print("\n");
            }
            die('Failed to connect to database server'."\n");
        } else {
            mysqli_report(MYSQLI_REPORT_OFF);
            @ $this->_dbref->select_db($this->_environment->getDatabase());
            if ($this->_dbref->sqlstate != "00000")	{
                $errorstring="<error date=\"".date(DATE_RFC822)."\">\n";
                $errorstring .= "\t<class> ".$this->_environment->getDBHost()." </class>\n";
                $errorstring .= "\t<sqlstate> ".$this->_dbref->sqlstate."</sqlstate>\n";
                $errorstring .= "\t<errorcode> ".$this->_dbref->errno."</errorcode>\n";
                $errorstring .= "\t<errortext> ".$this->_dbref->error." </errortext>\n";
                $errorstring .= "</error>\n";
                \Log::sql($errorstring);
                if (\Environment::flag('display_mysql_errors')) {
                    print($errorstring."\n\n");
                }
                $this->_connected = false;
            }
        }
        $this->_environment->clearPassword();
    }

    /**
     * Closes the DB connection
     */
    public function close() {
        //$this->_dbref->close();
        return $this;
    }

    public function _rowsAffected($rows=null) {
        if ($rows===null) {
            return $this->_rowsAffected;
        }
        $this->_rowsAffected = $rows;
        return $this;
    }
    
    /**
     * Executes a raw SQL query that is passed in
     *
     * @param string $query
     * @return array
     */
    public function rawQuery($qry) {
        return $this->_dbref->query($qry);
    }

    /**
     * Executes a query and records any issues with it
     *
     * @param type $query
     * @return array
     */
    public function query($qry)	{
        $this->_lastQuery($qry);
        $resultSet = null;
        $status = \Humble::cache('queryLogging');
        $logQuery  = ($status==='On');
        if ($this->_connected) {
            if ($logQuery) {
                $st = microtime(true);
            }
            $resultSet = $this->_dbref->query($qry);
            $this->_state = $this->_dbref->sqlstate;
            if ($logQuery) {
                \Log::query($qry."\n\nELAPSED TIME: ".(microtime(true)-$st)."\nSQL STATE: ".$this->_state."\nERROR: ".$this->_dbref->error."\n");
            }
            if (($this->_dbref->sqlstate != "00000")) {
                if ($this->_dbref->errno!=1062) {
                    $errorstring = "<error date=\"".date(DATE_RFC822)."\">\n";
                    global $namespace, $controller, $action;
                    if ($namespace && $controller && $action) {
                        $errorstring .= "<action> ".$namespace."/".$controller."/".$action." </action>";
                    }
                    $errorstring .= "\t<user_id>".((isset($_SESSION['uid'])) ? $_SESSION['uid'] : 'N/A')."</user_id>\n";
                    $errorstring .= "\t<host> ".$this->_environment->getDBHost()." </host>\n";
                    $errorstring .= "\t<sqlstate> ".$this->_dbref->sqlstate."</sqlstate>\n";
                    $errorstring .= "\t<errorcode> ".$this->_dbref->errno."</errorcode>\n";
                    $errorstring .= "\t<errortext> ".$this->_dbref->error." </errortext>\n";
                    $errorstring .= "\t<sql>\n";
                    $errorstring .= "\t\t$qry\n";
                    $errorstring .= "\t</sql>\n";
                    $errorstring .= "\t<rowsreturned>\n";
                    if ($resultSet) {
                        $errorstring .= "\t\t$resultSet->num_rows\n";
                    } else {
                        $errorstring .= "\t\tNot Available\n";
                    }
                    $errorstring .= "\t</rowsreturned>\n";
                  /*  ob_start();
                    debug_print_backtrace();
                    $errorstring .="\t<trace>".ob_get_clean()."</trace>\n"; */  //@TODO: suspending this until I can come up with something that doesnt clog up the log
                    $errorstring .= "</error>\n";
                    \Log::sql($errorstring);
                    if (php_sapi_name()=='cli') {
                        print($errorstring."\n");
                        debug_print_backtrace();
                        print("\n");
                    }                    
                }
            } else {
                $rs = $this->_dbref->query('SELECT ROW_COUNT() as ROWS_AFFECTED');
                $row = $rs->fetch_assoc();
                $this->_rowsAffected($row['ROWS_AFFECTED']);
            }
        } else {
            $errorstring="<error date=\"".date(DATE_RFC822)."\">\n";
            $errorstring .= "\t<class> ".$this->_environment->getDBHost()." </class>\n";
            $errorstring .= "\t<errortext> Not connected to any valid instance of MySQL </errortext>\n";
            $errorstring .= "\t<sql>\n";
            $errorstring .= "\t\t$qry\n";
            $errorstring .= "\t</sql>\n";
            $errorstring .= "\t<rowsreturned>\n";
            $errorstring .= "\t\tNot Available\n";
            $errorstring .= "\t</rowsreturned>\n";
            $errorstring .= "</error>\n";
            \Log::sql($errorstring);
        }
        if (is_object($resultSet) && $resultSet->num_rows) {
            $resultSet = $this->translateResultSet($resultSet);
        } else {
            $resultSet = [];
        }
	return $resultSet;
    }

    /**
     * Allows you to prepare a query for exectuion
     *
     * @param type $query
     */
    public function prepare($qry) {
	$this->_prep = $this->_dbref->prepare($qry);
    }

    /**
     * Executes a prepared query
     *
     * @param type $parms
     * @return type
     */
    public function execute($parms)	{
	return $this->execute($this->_prep,$parms);
    }

    /**
     * Begins transaction support
     *
     * @return object
     */
    public function beginTransaction()	{
	$resultSet = $this->_dbref->query("START TRANSACTION");
	return $resultSet;
    }

    /**
     * Commits a transaction
     *
     * @return object
     */
    public function endTransaction() {
	$resultSet = $this->_dbref->query("COMMIT");
	return $resultSet;
    }

    /**
     * Reverses one or more recent queries, up to the start of the transaction or last commit
     *
     * @return object
     */
    public function explicitRollBack() {
	$resultSet = $this->_dbref->query("ROLLBACK");
	return $resultSet;
    }

    /**
     * returns the last numeric id generated as a result of an insert
     *
     * @return int
     */
    public function getInsertId() {
	return $this->_dbref->insert_id;
    }

    /**
     * returns if connected to a database
     *
     * @return boolean
     */
    public function isConnected()   { return $this->_connected;  }

    /**
     * returns the action DB handler
     *
     * @return object
     */
    public function getDbref()      { return $this->_dbref;		}
}

?>
