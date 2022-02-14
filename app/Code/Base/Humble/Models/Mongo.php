<?php
namespace Code\Base\Humble\Models;
use Humble;
use MongoDB;

/**
 * Abstraction of Mongo, similar to how we abstract MySQL
 *
 * This class is to be used in environment supported by PHP 7 and higher
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 */
class Mongo  {

    private $_mongoDB       = false;
    private $_namespace     = false;
    private $_collection    = false;
    private $_mongo         = false;
    private $_db            = false;
    private $_connected     = false;
    private $_mongoServer   = false;
    private $_data          = [];
    private $_prefix        = null;
    private $_isVirtual     = null;

    /**
     * Constructor, allows for the setting of mongoDB, otherwise uses default
     *
     * @param type $db
     */
    public function __construct($db=false) {
        $this->_db = ($db) ? $db : false;
        $settings  = \Singleton::getSettings();
        $host      = $settings->getMongoDB();
        $user_id   = $settings->getMongoDBUserId();
        $password  = $settings->getMongoDBPassword();
        $this->_mongoServer($host = ($user_id && $password) ? $user_id.':'.$password.'@'.$host : $host);
        if (!$this->_mongo = new MongoDB\Client('mongodb://'.$host)) {
            die('Failed to establish a connection to MongoDB @ '.$settings->getMongoDB());
        }
    }

    /**
     * Cute routine to convert the next letter after an underscore to uppercase while removing the underscore
     *
     * @param string $string
     * @param boolean $first_char_caps
     * @return string
     */
    public function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }    
    
    /**
     * Where is mongo located at?
     *
     * @param string $arg
     * @return string
     */
    public function _mongoServer($arg=false) {
        if ($arg) {
            $this->_mongoServer = $arg;
            return $this;
        } else {
            return $this->_mongoServer;
        }
    }

    /**
     * Allows this to be reused... returns a itself to a pristine state
     *
     * @return \Code\Base\Humble\Models\Mongo
     */
    public function reset() {
        $this->_data = [];
        return $this;
    }

    /**
     * Connects to whatever DB and collection was passed in
     *
     */
    protected function connect() {
        if ($this->_mongo) {
            $db = $this->_namespace();
            if ($db) {
                $this->_db  = $this->_mongo->$db;
                $collection = $this->_collection();
                if ($collection) {
                    $this->_collection = $this->_db->$collection;
                    $this->_connected  = true;
                }
            }
        }
        return $this->_connected;
    }

    /**
     * Wraps the functionality in a try catch block to manage exceptions
     *
     * @param string $what
     * @return array
     */
    protected function _execute($what=false) {
        $data   = [];
        $srch   = [];
        if (!$this->_connected) {
            $this->connect();
        }
        $doc = $this->_document();
        if ($what && $this->_db && $this->_collection) {
            try {
                switch ($what) {
                    case "load"     :
                    case "findOne"  :
                        $doc = $this->_collection->findOne($doc);
                        $this->_document($doc);
                        break;
                    case "fetch"    :
                    case "find"     :
                    case "read"     :
                        $rows   = [];
                        foreach ($this->_collection->find($doc) as $row) {
                            $rows[] = $this->_map($row);
                        }
                        return $rows;  //hate to do this but we don't need to do additional processing on cursors
                        break;
                    case "save"     :
                    case "update"   :
                        $srch   = isset($doc['_id']) ? ['_id'=>$doc['_id']] : (isset($doc['id']) ? ['id'=>$doc['id']] : []);
                        $fields = ['$set'=>$doc];
                        $data   = $this->_collection->updateOne($srch,$fields,['upsert'=>true]);
                        $x      = $data->getUpsertedId();
                        if ($x) {
                            $doc['_id'] = $x;
                        } else {
                            //Really?  WTF?
                            $y = $this->_collection->findOne($doc);
                            if ($y && isset($y['_id'])) {
                                $doc['_id'] = $y['_id'];
                            }
                        }
                        //\Log::console("Matched/Modified document(s) [".$data->getMatchedCount().','.$data->getModifiedCount()."]");
                        break;
                    case "add"      : //if we got this, then it's a new record
                    case "insert"   :
                        $data = $this->_collection->insertOne($doc);
                        $doc['_id'] = (string)$data->getInsertedId();
                        break;
                    case "dbstats"  :
                        $doc = $this->_db->command([ 'dbStats' => 1 ]);
                        break;
                    case "stats"    :
                        /*
                         * @TODO:  Redo this, this functionality is done differently in MongoDB class as opposed to MongoClient
                         */
                        $doc    = $this->_db->command([ 'collStats' => 'test' ]);
                        break;
                    case 'truncate':
                        //Performs the equivalent of a MySQL truncate on a Mongo Collection
                        $doc    = $this->_collection->deleteMany([]);
                        break;
                    case 'drop':
                        $doc    = $this->_collection->drop();
                     break;
                    case "remove"   :
                    case "delete"   :
                        //don't want to remove too much... can only remove _id or id fields
                        $key = (isset($doc['id']) || isset($doc['_id']));
                        if ($key) {
                            $this->_collection->deleteOne($doc); //$doc->_id is a reference to a MongoID
                        } else {
                            \Log::console('MongoDB Error: Could not determine the "id" or "_id" key to remove document');
                        }
                        break;
                    default         :
                        break;
                }
            } catch (MongoCursorException $ex) {
                \HumbleException::mongo($ex);
            } catch (Exception $ex) {
                \HumbleException::mongo($ex);
            }
        }
        $doc = $this->_map($doc);
        foreach ($doc as $var => $val) {
            if ($var !== '_id') {
                $method = 'set'.$this->underscoreToCamelCase($var);
                $this->$method($val);
            }
        }
        return $doc;
    }

    /**
     * Relays the truncate command
     */
    public function truncate() {
        return $this->_execute('truncate');
    }

    /**
     * Relays the functions that return a set of rows
     *
     * @param type $doc
     * @return type
     */
    public function fetch($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('fetch');
    }
    public function read($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('read');
    }
    public function find($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('find');
    }

    /**
     * Relays the load/findOne functions
     *
     * @param type $doc
     * @return type
     */
    public function load($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('load');
    }
    public function findOne($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('load');
    }

    /**
     * Relays the write functions
     *
     * @param type $doc
     * @return type
     */
    public function write($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('write');
    }
    public function save($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('save');
    }
    public function insert($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('insert');
    }
    public function add($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('add');
    }

    /**
     * Relays the update function
     *
     * @param type $doc
     * @return type
     */
    public function update($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('update');
    }

    /**
     * Relays the delete function
     *
     * @param type $doc
     * @return type
     */
    public function delete($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('delete');
    }
    public function remove($doc=false) {
        if ($doc) {
            $this->_document($doc);
        }
        return $this->_execute('remove');
    }
    public function drop() {
        return $this->_execute('drop');
    }


    /**
     * Relays the stats function
     *
     * @param type $doc
     * @return type
     */
    public function stats($doc=false) {
        return $this->_execute('stats');
    }
    public function dbstats($doc=false) {
        return $this->_execute('dbstats');
    }

    /**
     * Records or returns the database name
     *
     * @param type $arg
     * @return type
     */
    public function _mongoDB($arg=false) {
        if ($arg) {
            $this->_mongoDB = $arg;
        } else {
            return $this->_mongoDB;
        }
        return $this;
    }

    public function _namespace($arg=false) {
        if ($arg) {
            $this->_namespace = $arg;
        } else {
            return $this->_namespace;
        }
        return $this;
    }

    public function _prefix($arg=false) {
        if ($arg) {
            $this->_prefix = $arg;
        } else {
            return $this->_prefix;
        }
        return $this;
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
     * Records or returns the collection name
     *
     * @param type $arg
     * @return type
     */
    public function _collection($arg=false) {
        if ($arg) {
            $this->_collection = $arg;
        } else {
            return $this->_collection;
        }
        return $this;
    }

    /**
     * Converts any objects to associative arrays
     *
     * @param type $doc
     * @return type
     */
    protected function _map($doc) {
        $mapped = [];
        if ($doc && (is_array($doc) || (is_object($doc)))) {
            foreach ($doc as $var => $val) {
                if ($var === '_id') {
                    $mapped['_id'] = (string)$val;
                } else {
                    $mapped[$var] = (is_object($val) || is_array($val)) ?  $this->_map($val) : $val;
                }
            }
        }
        return $mapped;
    }

    /**
     * If you did not specifically pass in a document, maybe I can build one for you.  Very important, if the variable _id is set, it will create a mongo object out of it
     *
     * @param mixed $doc
     * @return Humble_Model_Mongo or the document
     */
    public function _document($doc=false) {
        if ($doc!==false) {
            $this->_data = [];
            if ($doc) {
                foreach ($doc as $var => $val) {
                    $method = 'set'.$this->underscoreToCamelCase($var,true);
                    $this->$method($val);
                }
            }
            return $this;
        } else {
            $doc = [];
            foreach ($this->_data as $key => $val) {
                $method = 'get'.$this->underscoreToCamelCase($key,true);
                $val    = $this->$method();
                if (($key === '_id') && (!is_object($val))) {
                    $val = new \MongoDB\BSON\ObjectID($val);
                }
                if (is_numeric($val)) {
                    $val = $val + 0; //effectively does a cast to a numeric, since mongo respects types... "3" != 3
                }
                $doc[$key] = $val;
            }
            return $doc;
        }
    }

    /**
     * Returns a value from a magic method
     *
     *
     * @param string $name A pnuemonic, label or variable name
     *
     * @return string Variable value or response from remote resource
     */
    public function __get($name)  {
        $retval = null;
        if (!is_array($name)) {
            if (isset($this->_data[$name])) {
                $retval = $this->_data[$name];
            }
        }
        return $retval;
    }

    /**
     * The setter magic method.
     *
     * Just stores the name/value pair passed in to the internal array.
     *
     * @param string $name Name of variable in the name/value pair
     *
     * @param string $value Value of variable in the name/value pair
     */
    public function __set($name,$value) {
        $this->_data[$name] = $value;
        return $this;
    }

    /**
     * Magic method to handle non-existant methods being invoked
     *
     * Whenever a method is called that doesn't exist, this method traps the name
     * of the method, and any arguments.
     *
     * @param string $name The name of the method
     * @param array $arguments arguments passed to the non-existant method
     */
    public function __call($name, $arguments)    {
        $token = lcfirst(substr($name,3));
        if (substr($name,0,3)=='set') {
            return $this->__set($token,$arguments[0]);
        } else if (substr($name,0,3)=='get') {
            $result = $this->__get($token);
            return $result;
        } else {
            \Log::console("Undefined Method: ".$name." invoked from ".$this->getClassName().".");
        }
    }
}
?>