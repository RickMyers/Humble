<?php
namespace Code\Framework\Humble\Entities;
use Humble;
use Environment;
use Log;
class Unity 
{
    use \Code\Framework\Humble\Traits\Base;
    
    protected $_entity        = null;
    protected $_keys          = [];
    protected $_column        = [];
    protected $_fields        = [];
    protected $_orderBy       = [];
    private   $_orderBuilt    = false;
    protected $_fieldList     = "*";
    protected $_db            = null;
    protected $_search        = [];
    protected $_autoinc       = [];
    protected $_currentPage   = 0;
    protected $_rowCount      = 0;
    protected $_page          = 0;
    protected $_rows          = 0;
    protected $_fromRow       = 0;
    protected $_toRow         = 0;
    protected $_cursor        = false;
    protected $_cursorId      = 0;
    protected $_rowsReturned  = 0;
    protected $_conditions    = [];
    protected $_distinct      = false;
    protected $_mongodb       = null;
    protected $_mongocollection = null;
    protected $_mongoJoin     = 'id';
    protected $_polyglot      = null;
    protected $_module        = null;
    protected $_headersSent   = false;
    protected $_headers       = [];
    protected $_clean         = true;                                           //if polyglot, clean out MongoDB _id references in the result set
    protected $_translation   = false;                                          //if true, parse result set looking for tokens and replace them with corresponding value from lookup table
    private   $_collections   = [];                                             //a hash table of connections to be used in a polyglot transaction.  This is used to improve performance
    protected $_dynamic       = false;                                          //Dynamically build the where clause on my query?    
    protected $_alias         = false;
    protected $_batchsql      = [];
    protected $_batch         = false;
    protected $_actual        = false;
    protected $_resource      = false;
    protected $_inField       = '';
    protected $_in            = [];
    protected $_betweenField  = '';
    protected $_between       = '';
    protected $_iv            = 'Humble Framework';                             //encryption initialization vector
    protected $_noLimitQuery  = '';                                             //Current query before pagination is added
    protected $_xref          = [];                                             //Hash array for xrefing the names of result set columns to something else
    protected $_json          = false;
    protected $_exclude       = false;
    protected $_bulk          = false;
    public    $_lastResult    = [];

    /**
     * Initial constructor
     *
     * If this is a polyglot transaction, use the $this->query() function since
     * it performs the necessary checks.  Otherwise it is ok to just go against
     * the $this->_db->query() direct DB call, which bypasses mongodb
     *
     */
    public function __construct() {
        $this->_db = Humble::connection($this);
    }
    
    /**
     * Can set the Initialization Vector for SSL encryption/decryption or just return the current value for that vector
     * 
     * @param mixed $vector
     * @return string
     */
    public function iv($vector=false) {
        if ($vector) {
            $this->_iv = $vector;
            return $this;
        }
        return $this->_iv;
    }
    
    /**
     *
     */
    public function __destruct()   {
        if ($this->_page()) {
            if (!(php_sapi_name() === 'cli')) {
                foreach ($this->_headers as $header => $val) {
                    header($header.': '.$val);
                }
            }
        }        
    }

    /**
     *
     */
    public function getClassName()  {
        return __CLASS__;
    }
    
    protected function underscoreToCamelCase($string, $first_char_caps=false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
    
    /**
     * Sets the ORM back to pristine state ready to run another query
     * 
     * @return $this
     */
    public function clean()  {
        $this->_decrypt      = false;
        $this->_encrypt      = false;
        $this->_fields       = [];
        $this->_orderBy      = [];
        $this->_search       = [];
        $this->_data         = [];
        $this->_noLimitQuery = '';
        return $this;
    }
    
    /**
     *
     */
    public function reset()  {
        $this->clean();
        return $this;
    }
    
    /**
     * What is the actual table name?  Use this for when you want to use Unity on a table that doesn't follow the standard convention
     * 
     * @param mixed $actual
     * @return mixed
     */
    public function _actual($actual=false) {
        if ($actual!==false) {
            $this->_actual = $actual;
            return $this;
        }
        return $this->_actual;
    }
    
    /**
     * Identifies the "resource", or external query, that we are going to read, process, and execute
     * 
     * @param string $resource
     * @return mixed
     */
    public function _resource($resource=false) {
        if ($resource!==false) {
            $this->_resource = $resource;
            return $this;
        }
        return $this->_resource;
    }
    
    /**
     * Because of the variable columns per row, we need to get a list of all columns across our dataset
     * 
     * @param array $rows
     * @return array
     */
    private function columns($rows) {
        $columns    = [];
        foreach ($rows as $row) {
            foreach ($row as $column => $value) {
                $columns[$column] = $column;
            }
        }
        return $columns;
    }
    
    /**
     * Because of the variable number of columns per row in a polyglot entity, this method "normalizes" the result set, which means all rows will have the same number of columns
     * 
     * @param iterator $iterator (optional
     * @return iterator
     */
    protected function normalize($iterator=false) {
        $iterator = ($iterator) ? $iterator : $this->fetch();
        $results  = $iterator->toArray();
        $columns  = $this->columns($results);
        foreach ($results as $idx => $row) {
            $newRow = [];
            foreach ($columns as $column) {
                $newRow[$column] = isset($row[$column]) ? $row[$column] : '';
            }
            $results[$idx] = $newRow;
        }
        return $iterator->set($results);
    }
    
    /**
     *
     */
    public function distinct($field=false) {
        $retval  = [];
        $table   = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        if ($field) {
            $query = <<<SQL
                select distinct {$field} from {$table}
SQL;
            $query .= $this->buildWhereClause(true);
            $query .= $this->buildOrderByClause();
            $retval = $this->query($query);
        }
        return $retval;
    }

    /**
     * Passing in either a namespace/collection or just a collection name will join the MySQL result set with the identified collection
     *
     * @param string $mongo_source
     * @return $this
     */
    public function with($mongo_source=false) {
        if ($mongo_source) {
            $this->_polyglot = true;
            if (strpos($mongo_source,'/') !== false) {
                $parts = explode('/',$mongo_source);
                $parts[0] = ($parts[0]==='default') ? Environment::namespace() : $parts[0];
                $data  = Humble::module($parts[0]);
                $this->_mongodb = $data['mongodb'];
                $this->_mongocollection = str_replace(["/"],["_"],substr($mongo_source,strpos($mongo_source,'/')+1));
            } else {
                $data  = Humble::module($this->_namespace());
                $this->_mongodb = $data['mongodb'];
                $this->_mongocollection = $mongo_source;
            }
        }
        return $this;
    }

    /**
     * Allows you to specify the name of a field to join on when the field we are going to use to join a MySQL result with MongoDB is not the default "id"
     *
     * @param string $field
     * @return $this
     */
    public function on($field=false) {
        if ($field!==false) {
            $this->_mongoJoin = $field;
        }
        return $this;
    }

    
    public function bulk($number=false) {
        if ($number===false) {
            return $this->_bulk;
        }
        $this->_bulk = $number;
        return $this;
    }
    
    protected function parseResource($lines) {
        $query = '';
        foreach ($lines as $line) {
            if (count($segments = explode('%%',$line))>1) {
                $line = ''; $keep = false;
                foreach ($segments as $idx => $segment) {
                    if ($idx % 2 === 1) {
                        if (isset($_REQUEST[$segment])) {
                            $keep = $segments[$idx] = $_REQUEST[$segment];
                        }
                        $line = ($keep) ? implode("",$segments) : '';
                    }
                }
            } 
            $query .= $line;
            
        }
        return $query;
    }
    
    public function _manageSQLResource() {
        if ($resource = $this->_resource()) {
            if ($namespace = $this->getNamespace()) {
                if ($module = \Humble::module($namespace)) {
                    if (file_exists($resource = 'Code/'.$module['package'].'/'.$module['resources_sql'].'/'.(str_replace('.sql','',$resource).'.sql'))) {
                        $query = $this->parseResource(explode("\n",file_get_contents($resource)));
                        return $this->query($query);
                    }
                }
            }
        }
    }
    /**
     * What this will do is take a comma separated list of column names and will swap them out for new names in the return result set
     * 
     * Format:  new_column_name1=old_column_name1,new_column_name2=old_column_name2 etc...
     * 
     * @param mixed $list
     * @return $this
     */
    public function _xref($list=[]) {
        if ($list) {
            if (is_string($list)) {
                $pairs = explode(',',$list);
                $list  = [];
                foreach ($pairs as $pair) {
                    $parts = explode('=',$pair);
                    $list[$parts[0]] = $parts[1];
                }
            }
            $this->_xref = $list;
        }
        return $this;
    }
    
    /**
     * Whether to format the array to json or not
     * 
     * @param type $json
     * @return mixed
     */
    public function _json($json=null) {
        if ($json !== null) {
            $this->_json = $json;
            return $this;
        }
        return $this->_json;
    }
    
    /**
     * For when XREF is present, do we exclude fields not on the XREF list?
     * 
     * @param string $exclude
     * @return $this
     */
    public function _exclude($exclude=false) {
        if ($exclude) {
            $this->_exclude = $exclude;
            return $this;
        }
        return $this->_exclude;
    }
    
    /**
     *
     */
    public function describe() {
        $table   = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query   = <<<SQL
          describe {$table}
SQL;
        return $this->_db->query($query);
    }

    /**
     *
     */
    protected function addLimit($page) {
        $query = '';
        if ($this->_rows()) {
             if ($this->_page() && $page) {
                  $this->_fromRow($pre = (($page-1) * $this->_rows()));
                  $this->_toRow($page * $this->_rows());
                  $query .= ' limit '.$pre.','.$this->_rows();
             } else if ($this->_cursor()) {
                 $query .= ' limit '.$this->_rows();
             }
        }
        return $query;
    }

    /**
     * Adds an arbitrary condition to the where clause
     * 
     * @param string $condition
     * @return $this
     */
    public function condition($condition=false) {
        if ($condition) {
            $this->_conditions[] = $condition;
            return $this;
        } else {
            return $this->_conditions;
        }
    }

    /**
     * 
     * 
     * @param string $list
     * @return array
     */
    public function _fieldList($list='') {
        if ($list === '') {
            return $this->_fieldList;
        } else {
            $this->_fieldList = $list;
        }
    }

    /**
     * Will remove all rows from a table and reset auto incrementing ID
     * 
     * @return type
     */
    public function truncate()  {
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query = <<<SQL
        truncate table {$table}
SQL;
        return $this->_db->query($query);
    }

    /**
     * Deprecated
     */
    public function average($field)  {
        $group = [];
        foreach ($this->_keys as $idx => $key) {
            $method = 'get'.ucfirst($idx);
            $res = $this->$method();
            if ($this->$method() != "") {
                $group[$idx] = $idx;
            }
        }
        $group = implode(',',$group);
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query = "select {$group},coalesce(avg({$field}),'0') as `average` from ".$table;
        $results = $this->_db->query($query);
        return $results[0]['average'];
    }

    /**
     *@TODO: Rework this to remove FOUND_ROWS() deprecated function
     * 
     * This is likely going for deprecation
     */
    public function search($field=false,$text=false)   {
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();        
        if (($field !== false) && ($text !== false)) {
            $query = <<<SQL
            select SQL_CALC_FOUND_ROWS * from {$table}
             where {$field} like '%{$text}%'
SQL;
        } else {
            $results = [];
            if (($field===true) && ($text===false)) {
                //basically, you passed a true in the first field and nothing in the second,
                //this means you want to also search the key fields (quasi-polymorphic)
                foreach ($this->_keys as $idx => $key) {
                    $method = 'get'.ucfirst($idx);
                    $results[$idx] = $this->$method();
                }
            }
            foreach ($this->_fields as $idx => $key) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
            $countRowClause = ($this->_rows() && $this->_page()) ? " SQL_CALC_FOUND_ROWS " : "";
            $query   = "select {$countRowClause} * from {$table}";
            $orFlag = false;
            foreach ($results as $field => $value) {
                if ($results[$field]!="") {
                     if ($orFlag == false) {
                        $query .= ' where ';
                    }
                    $query .= ($orFlag ? "or ": "").$field." like '%".addslashes($value)."%' ";
                    $orFlag = true;
                }
            }
            $query .= $this->addLimit($this->_page());
        }
        $results = $this->_db->query($query);
        $query = <<<SQL
         select FOUND_ROWS()
SQL;
        $rows = $this->_db->query($query);
        $this->_rowCount($rows[0]['FOUND_ROWS()']);
        return $results;
    }

    /**
     * Counts total rows in the table
     * 
     * @return int
     */
    public function totalRows()  {
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query = <<<SQL
            select count(*) as total from {$table}
SQL;
        $results = $this->_db->query($query);
        return (count($results) == 1) ? $results[0]["total"] : 0;
    }

    /**
     * Returns a specific row from the table
     * 
     * @param int $rowNum
     * @return array
     */
    public function fetchRow($rowNum)  {
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query = <<<SQL
        select * from {$table}
            limit $rowNum,1
SQL;
        $results = $this->query($query);
        return (count($results) == 1) ? $results[0] : null;
    }

    /**
     * Collects fields/ids to build an in clause later
     * 
     * @param type $args
     * @return $this
     */
    public function in($args=false) {
        if ($args) {
            if (is_array($args)) {
                foreach ($args as $arg) {
                    $this->_in[] = addslashes($arg);
                }
            } else {
                $this->_in[] = addslashes($args);
            }
        }
        return $this;
    }
    
    /**
     * Creates a between condition for the query
     * 
     * @param array $args
     * @return $this
     */
    public function between($args=false) {
        if ($args) {
            $this->_between = $args;
        } else {
            return $this->_between;
        }
        return $this;
    }
    
    /**
     * Returns a single row in array form from the table
     * 
     * By default, load uses the ID field of the table, but pass in true to let
     * it load by other fields
     * 
     * @param type $nonkeys
     * @return bool
     */
    public function load($nonkeys=false)  {
        $results    = [];
        $polyfields = [];
        foreach ($this->_keys as $idx => $key) {
            //we check to see if the a key value was attempted to be set, even if it was set to a null
            if (isset($this->_data[$idx]) || (array_key_exists($idx,$this->_data) && ($this->_data[$idx] === null))) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
        }
        if ($nonkeys) {
            $polyglot = $this->_polyglot();
            foreach ($this->_fields as $idx => $key) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
        }
        $table      = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query      = "select * from ".$table;
        $andFlag    = false;
        foreach ($results as $field => $value) {
            if (isset($this->_keys[$field]) || isset($this->_column[$field])) {
                if ($andFlag == false) {
                    $query .= ' where ';
                }
                if ($value === null) {
                    $query .= ($andFlag ? " and `": "`").$field."` is NULL ";
                } else {
                    $query .= ($andFlag ? " and `": "`").$field."` = '".addslashes($value)."' ";
                }
                $andFlag = true;
            } else {
                $polyfields[$field] = $value;
            }
        }
        if (!$andFlag) {
            Log::console('Entity error:  No index [id] field found, likely a configuration error, or you should be doing your lookup using non-key fields.  Check the config.xml and make sure the entity is listed in the ORM section ['.$this->_prefix().$this->_entity().']');
            return false; //no field found to index on, so we load nothing
        } else {
            $query .= ' LIMIT 1'; //load returns the first instance to match only
        }

        $row_total  = count($results = $this->query($query));                    //This is where the query happens
        $result     = $results->first();
        if ($this->_polyglot() && ($row_total>0)) {
            //now get the mongo object...
            $mod = $this->_module();
            if (isset($this->_collections[$mod['mongodb'].'/'.$this->_entity()])) {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()]->reset();
            } else {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()] = Humble::collection($mod['mongodb'].'/'.$this->_entity());
            }
            if ($id  = isset($result['id']) ? $result['id'] : (isset($result['uid']) ? $col['result'] : false)) {
                $mdb->setId($id);
                $rows = $mdb->load();
                if ($rows) {
                    foreach ($rows as $key => $val) {
                        if (!isset($result[$key])) {                            //If there's already a column with that name in the result, don't override it
                            $result[$key] = $val;
                        }
                    }
                }
            }
        }
        if ($row_total>0) {
            foreach ($result as $field => $value) {
                if ($field !== '_id') {
                    $method = 'set'.$this->underscoreToCamelCase($field,true);
                    $this->$method($value);
                }
            }
        }
        //now if polyglot and there are extra polyglot fields, go through this and exclude if they don't match
        if ($result && $this->_translation) {
            foreach ($result as $key => $val) {
                $result[$key] = Humble::string($val);
            }
        }
        $this->_lastResult->snip();
        return $this->_json() ? json_encode($result,JSON_PRETTY_PRINT) : $result;
  //      return Humble::array($result); ///someday
    }

    /**
     * Builds a where clause for the query
     * 
     * @param boolean $useKeys
     * @return string
     */
    protected function buildWhereClause($useKeys) {
        $query   = '';
        $results = [];
        if ($useKeys) {
            foreach ($this->_keys as $idx => $key) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
        }
        foreach ($this->_fields as $idx => $key) {
            $method = 'get'.ucfirst($idx);
            $results[$idx] = $this->$method();
        }
        $andFlag = false;
        foreach ($results as $field => $value) {
            if ($results[$field]!="") {
                 if ($andFlag == false) {
                    $query .= ' where ';
                }
                $query .= ($andFlag ? "and ": "").($this->_alias() ? $this->_alias().'.':'')."`".$field."` = '".addslashes($value)."' ";
                $andFlag = true;
            }
        }
        if ($this->_in) {
            $query .= ($andFlag) ? " and " : ' where ';
            $query .= "`".$this->_inField."` in ('".implode("','",$this->_in)."') ";
            $andFlag = true;
        }
        if ($this->_between) {
            $query .= ($andFlag) ? " and " : ' where ';
            $query .= "`".$this->_betweenField."` between '".$this->_between[0]."' and '".$this->_between[1]."' ";
            $andFlag = true;
        }
        if ($this->condition()) {
            foreach ($this->condition() as $condition) {
                $query .= ($andFlag) ? " and " : ' where ';
                $query .= " ".$condition." ";                
                $andFlag = true;
            }
        }   
        if ($this->_cursor()) {
            $query .= ($andFlag) ? " and " : ' where ';
            $query .= $this->_prefix().$this->_entity().'.id > '.$this->_cursor.' ';
        }        
        return $query;
    }

    /**
     * Builds an order by clause for the query
     * 
     * @return string
     */
    protected function buildOrderByClause() {
        $query = '';
        if (count($this->_orderBy) > 0) {
            $query .= ' order by ';
            $ctr = 0;
            foreach ($this->_orderBy as $field => $direction) {
                if ($ctr) {
                    $query .= ', ';
                }
                $query .= '`'.$field.'` '.$direction;
                $ctr++;
            }
        }
        if ($query) {
            $this->_orderBuilt = true;
        }
        return $query;
    }

    /**
     * Fetches a dataset, normally looks at just fields, pass in true to get it to include the ID primary key in the query
     * 
     * @param boolean $useKeys
     * @return iterator
     */
    public function fetch($useKeys = false) {
        if ($this->_page()) {
            $this->_currentPage($this->_page());
        }
        $table   = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query   = "select ". $this->_distinct() ." ".$this->_fieldList()." from ".$table;
        $query  .= $this->buildWhereClause($useKeys);
        $this->_noLimitQuery = $query;                                          //for pagination purposes        
        $query  .= $this->buildOrderByClause();
        $query  .= $this->addLimit($this->_currentPage);
        return $this->query($query);
    }

    /**
     * Get the max id in the result set
     * 
     * @param iterator $rows
     * @return int
     */
    protected function cursorId(&$rows): int {
        $max_id = 0;
        foreach ($rows as $row) {
            $max_id = ($row['id'] ?? 0) > $max_id ? $row['id'] : $max_id;
        }
        $this->_cursor($max_id);
        return $max_id;
    }
    
    /**
     * Determines pagination values
     * 
     * @param string $query
     * @param iterator $results
     * @return $this
     */
    protected function calculateStats($query,&$results) {
        $rows = $this->_db->query($query);
        $this->_rowCount($rows[0]['FOUND_ROWS']);
        if ($this->_rowCount()) {
            if ($this->_page()) {
                if ($this->_toRow() > $this->_rowCount()) {
                    $this->_toRow($this->_rowCount());
                }
                $this->_fromRow($this->_rows() * ($this->_page()-1)+1);
                $this->_headers['pagination'] = json_encode([
                    'rows' => [
                        'from'  => $this->_fromRow(),
                        'to'    => $this->_toRow(),
                        'total' => $this->_rowCount()
                    ],
                    'pages' => [
                        'current' => $this->_page(),
                        'total'   => $this->_pages()
                    ]
                ]);
            } else if ($this->_cursor()) {
                $this->cursorId($results);
                $this->_rowsReturned(count($results));
                $this->_pages(floor($this->_rowsReturned() / $this->_rows()));
                $this->_headers['pagination'] = json_encode([
                    'cursor_id' => $this->_cursor(),
                    'pages' => [
                        'total' => $this->_pages()
                    ],
                    'rows' => [
                        'returned' => $this->_rowsReturned(),
                        'total' => $this->_rowCount()
                    ]
                ]);
            }
        }
        return $this;
    }

    /**
     *
     */
    private function implode_keys($char,$arr) {
        $result = '';
        foreach ($arr as $key => $value) {
            $result .= (($result==='') ? '' : $char ).$key;
        }
        return $result;
    }

    /**
     *  This save is multi-purpose... works like both an add/save as well as utilizing the polyglot feature
     *
     *  @return int
     */
    public function save() {
        $mod    = $this->_module();
        $mdb    = false;
        if ($this->_polyglot()) {
            if (isset($this->_collections[$mod['mongodb'].'/'.$this->_entity()])) {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()]->reset();
            } else {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()] = Humble::collection($mod['mongodb'].'/'.$this->_entity());
            }
            //if polyglot, we have to perform a "load" first to fetch the mongo stuff, or else we might accidentally lose the mongo stuff
            //$saved_data = $this->_data;
            //@TODO: IMPLEMENT THIS! or maybe not?  Maybe we are fine...

        }
        $db_fields  = [];
        foreach ($this->_keys as $idx => $key) {
            $method = 'get'.ucfirst($idx);
            $data   = $this->$method();
            if ($data) {
                $db_fields[$idx] = $data;
            }
        }
        $non_values = [];
        foreach ($this->_fields as $key => $value) {
            $method   = 'get'.ucfirst($key);
            $data     = $this->$method();
            if (isset($this->_column[$key]) || (isset($this->_keys[$key]))) {
                $db_fields[$key] = $data;
            } else {
                $non_values[$key] = $data;
            }
        }
        if (!isset($db_fields['modified'])) {
            $db_fields['modified'] = date('Y-m-d H:i:s');
        }
        $fieldlist  = '`'.$this->implode_keys('`,`',$db_fields).'`';
        $values = '';
        foreach ($db_fields as $field) {
            $values .= $values ? "," : "";
            $values .= ($field || ($field===0) || ($field==='0')) ? "'".addslashes($field)."'" : "NULL";
        }
        $duplicates = [];
        foreach ($db_fields as $key => $value) {
            if (isset($this->_column[$key])) {
                $duplicates[$key] = $db_fields[$key];
            }
        }
        $duplicate  = '`';
        foreach ($duplicates as $key => $value) {
            $duplicate .= (($duplicate === '`') ? '' : ",`").$key."` = ";
            $duplicate .= ($value || ($value===0) || ($value==='0')) ? "'".addslashes($value)."'" : "NULL";
        }
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query = <<<SQL
            insert into {$table}
                ({$fieldlist})
            values
                ({$values})
SQL;
        if ($duplicate != '`')
            $query .= <<<SQL
              on duplicate key update
                {$duplicate}
SQL;
        $this->_db->query($query);
        $insertId = $this->_db->getInsertId();
        if (!$insertId && !$this->getId()) {
            $d = $this->load(true);
            if (isset($d['id'])) {
                $insertId = $d['id'];
            }
        }
        if ($this->_polyglot()) {
            //first we look for mongo record with the same ID value
            foreach ($this->_keys as $key => $data) {
                if (($key === 'id')) {
                    $method = 'get'.ucfirst($key);
                    $id = $this->$method();
                    $mdb->setId(($id) ? $id : $insertId);
                    $d = $mdb->load();
                    if (!$d) {
                        //if nothing is returned since this is the initial save of the record, the ID just got toasted, so reset it
                        $mdb->setId(($id) ? $id : $insertId);
                    }
                }
            }
            //then we add/update with new values
            foreach ($non_values as $key => $value) {
                if ($key !== '_id') {
                    $method = 'set'.ucfirst($key);
                    $mdb->$method($value);
                }
            }
            //then we save
            $mdb->save();
        }
        return (($insertId) ? $insertId : $this->getId());
    }

    /**
     * Don't use add anymore, just use "save()"... they basically do the same thing
     * 
     * @return int
     */
    public function add()  {
        $fields     = $this->_fields;
        foreach ($this->_autoinc as $var => $autoinc) {
            $method = 'get'.ucfirst($var);
            if ($this->$method()) {
                $fields[$var] = true;
            } else if ($autoinc != 'Y') {
                $fields[$var] = true;
            }
        }
        $fieldlist  = '`'.$this->implode_keys('`,`',$fields).'`';
        $values     = [];
        foreach ($fields as $key => $value) {
            $method = 'get'.ucfirst($key);
            $values[] = addslashes($this->$method());
        }
        $values = "'".implode("','",$values)."'";
        $table  = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query  = <<<SQL
            insert into {$table}
                ({$fieldlist})
            values
                ({$values})
SQL;
        $this->_db->query($query);
        //polyglot action done here
        //@TODO: Got to refactor this like "Save" so that it takes into account polyglot tables
        return $this->_db->getInsertId();
    }

    /**
     * This thing is oh so not simple... but it works!
     *
     * @param type $query
     * @return type
     */
    public function query($query='') {
        if (!$query) {
            return $query;
        }
        if ($this->_dynamic()) {
            $query .= $this->buildWhereClause(true);
        }
        if (!$this->_orderBuilt && (count($this->_orderBy)>0)) {
            $query .= $this->buildOrderByClause();
        }
        $noLimit      = [];
        $words        = explode(' ',trim($query));
        $words[0]     = strtoupper(trim($words[0]));
        $noLimitQuery = ($this->_noLimitQuery) ? $this->_noLimitQuery : $query; //used for pagination
        if ($words[0]==='SELECT') {
            if ($this->_page()) {
                if ($noLimitQuery) {
                    $include = false;                                           //To create the pagination query, we need to drop the column section...
                    foreach (explode(' ',trim($noLimitQuery)) as $idx => $word) {
                        if ($token = ($idx) ? (($include = $include || (strtoupper($word)==='FROM')) ? $word : false) : $word.' COUNT(*) AS FOUND_ROWS') {   //this is a total d-bag line... i love it!
                            $noLimit[] = $token;
                        }
                    }
                }
                /* I need to look and see if there's already a limit statement AT THE END of the query.
                 * There could be a limit elsewhere, so I am checking to see
                 * if the word LIMIT is present in the last 6 or so words... if so, I don't add a limit
                 */
                $ctr = 0; $limitFound = false;                
                foreach (array_reverse($words) as $token) {
                    $ctr++;
                    $limitFound = (strpos(strtoupper($token),'LIMIT')!==false);
                    if ($limitFound || ($ctr>5)) {
                        break;
                    }
                }
                $query = implode(' ',$words);
                if (!$limitFound) {
                    $query = $query .' '. $this->addLimit($this->_page());
                }
                $noLimitQuery = implode(' ',$noLimit);
            }
        }
        if ($this->_batch && ($words[0] !== "SELECT")) {                          //for insert, update, and deletes we want to enable batch operations
            $this->_batchsql[] = $query;
            if (count($this->_batchsql) >= $this->_batch) {
                //@TODO: now go execute the batch sql statements
                $this->_batchsql = []; //reset the sql buffer
            }
            return false;
        }
        $results = $this->_db->query($query);
        //\Log::error($query);
        if ($this->_page() || $this->_cursor()) {
            $this->calculateStats($noLimitQuery,$results);
        }
        if ($this->_polyglot()) {
            //now get the mongo document and merge with the mysql row...
            $mJoin  = $this->_mongoJoin;      //what field in the query will we join with the mongo db, default is 'id' but can be any field in the result set
            if (!$this->_mongodb) {
                $mod = $this->_module();
                $this->_mongodb = $mod['mongodb'];
            }
            //Get the list of IDs to join with...
            $ids    = [];
            foreach ($results as $row) {
                if ($id = (isset($row[$mJoin]) ? $row[$mJoin] :  false)) {
                    $ids[] = (int)$id;
                }
            }
            if (!count($ids)) {
                //nop - no ids to join with so we all go home
            } else {
                $entity = ($this->_mongocollection) ? $this->_mongocollection : $this->_entity();   //If you had used the "with()" function, you'd have set mongodb variable, otherwise use the entity name from the MySQL query
                $mdb    = Humble::collection($this->_mongodb.'/'.$entity);
                if ($rows = $mdb->setId(['$in'=>$ids])->fetch()) {
                    $int = []; //$key = false;
                    //this builds a reference of every mysql result row that has the join field on it
                    foreach ($results as $idx => $row) {
                        if ($id = isset($row[$mJoin]) ? $row[$mJoin] : false) {
                            if (!isset($int[$id])) { 
                                $int[$id] = [];
                            }
                            $int[$id][] = $idx;
                        }
                    }
                    if (count($int)) {
                        //And this craziness merges the mongod document data with the mysql data, being careful not to overlay mysql field values with mongo field values if they both happen to have the same field
                        foreach ($rows as $row) {
                            if (isset($int[$row['id']])) {
                                foreach ($int[$row['id']] as $index => $vindex) {
                                    foreach ($row as $var => $val) {
                                        if (isset($results[$int[$row['id']][$index]]) && !isset($results[$int[$row['id']][$index]][$var])) {
                                            $results[$int[$row['id']][$index]][$var] = $val;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } 
        if ($this->_xref) {
            if ($this->_exclude) {
                $flip = array_flip($this->_xref);
            }            
            foreach ($results as $idx => $row)
                foreach ($row as $key => $value) {
                    if (!isset($this->_xref[$flip[$key]])) {
                        unset($results[$idx][$key]);
                        continue;
                    }
                    foreach ($this->_xref as $new_col => $old_col) {
                        if (isset($results[$idx][$old_col])) {
                            $results[$idx][$new_col] = $results[$idx][$old_col];
                            unset($results[$idx][$old_col]);
                        }
                    }
                }
        }        
        $results = Humble::model('humble/iterator')->clean($this->_polyglot() && $this->_clean())->withTranslation($this->_translation)->set($results);  //is this backwards?
        if (\Environment::isActiveDebug()) {
            \Log::user(array_merge(['Query'=>$query],$results->toArray()));
        }
        return $this->_lastResult = (($this->_normalize()) ? $this->normalize($results) : $results);
    }

    /**
     * Deletes one or more rows from a table, but will not do a delete if no condition is found
     * 
     * By default, will only delete by using an ID (primary key), pass in true 
     * to use fields to construct the delete clause
     * 
     * @param boolean $useFields
     */
    public function delete($useFields=false) {
        $results = [];
        foreach ($this->_keys as $idx => $key) {
            $method = 'get'.ucfirst($idx);
            $results[$idx] = $this->$method();
        }
        if ($useFields) {
            foreach ($this->_fields as $idx => $key) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
        }
        $andFlag = false;
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query   = "delete from ".$table;
        $conditionFound = false;
        if ($useFields) {
            if ($whereClause = $this->buildWhereClause(true)) {
                $conditionFound = true;
                $query .= $whereClause;
            }
            
        } else {
            foreach ($results as $field => $value) {
                if ($results[$field]!="") {
                    $conditionFound = true;
                     if ($andFlag == false) {
                        $query .= ' where ';
                    }
                    $query .= ($andFlag ? "and ": "").$field." = '".addslashes($value)."' ";
                    $andFlag = true;
                }
            }
        }
        if ($conditionFound) {
            $this->_db->query($query);
            //POLYGLOT check here
            //@TODO: Implement a check to see if this is a polyglot table, and remove corresponding row in MongoDB
        } else {
            Log::console('Ignoring delete ['.$query.'] since no condition for the delete was found');
        }
    }

    /**
     * A bridge for doing deletes from a controller without a models help
     */
    public function nonkeysdelete() {
        $this->delete(true);
    }
    /**
     * Fetches the row from the entity with the next higher ID
     *
     * @return array
     */
    public function next() {
        $data   = null;
        $id     = $this->getId(); //If no id, not worth doing
        $table  = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        if ($id) {
            $this->reset();
            $query  = <<<SQL
                select id
                  from {$table}
                 where id > {$id}
                 order by id asc
               limit 1
SQL;
            $results = $this->query($query)->toArray();
            if ($results && isset($results[0])) {
                $data = $this->reset()->setId($results[0]['id'])->load();
            }
        }
        return $data;
    }

    /**
     * Fetches the row from the entity with the next higher ID
     *
     * @return array
     */
    public function previous() {
        $data   = null;
        $id     = $this->getId(); //If no id, not worth doing
        if ($id) {
            $this->reset();
            $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
            $query  = <<<SQL
                select id
                  from {$table}
                 where id < {$id}
                 order by id desc
                 limit 1
SQL;
            $results = $this->query($query)->toArray();
            if ($results && isset($results[0])) {
                $data = $this->reset()->setId($results[0]['id'])->load();
            }
        }
        return $data;
    }

    /**
     * Shorthand for the 'previous' method...
     *
     * @return type
     */
    public function prev() {
        return $this->previous();
    }

    /**
     *
     */
    public function lock() {
        //initializes a transaction
    }

    /**
     *
     */
    public function unlock() {
        //commits the transaction
    }

    public function commit() {
       $this->_db->endTransaction();
    }
    /**
     *
     */
    public function count($useKeys=true)
    {
        $results = [];
        if ($useKeys) {
            foreach ($this->_keys as $idx => $key) {
                $method = 'get'.ucfirst($idx);
                $results[$idx] = $this->$method();
            }
        }
        foreach ($this->_fields as $idx => $key) {
            $method = 'get'.ucfirst($idx);
            $results[$idx] = $this->$method();
        }
        $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
        $query    = "select count(*) as count from ".$table;
        $andFlag = false;
        foreach ($results as $field => $value) {
            if ($results[$field]!="") {
                 if ($andFlag == false) {
                    $query .= ' where ';
                }
                $query .= ($andFlag ? "and ": "").$field." = '".addslashes($value)."' ";
                $andFlag = true;
            }
        }
        $results = $this->_db->query($query);
        return ((count($results)>0) ? $results[0]['count'] : 0);
    }

    /**
     * When updating modules, this needs to be called to expire the old cache and recreate it
     */
    public function recache() {
        $this->loadEntityKeys(false);
        $this->loadEntityColumns(false);
    }

    public function clone($resource=false) {
        if ($resource) {
            
        }
    }
    /**
     * Gets rows from table where the ID is greater than a set amount
     * 
     * @param type $id
     * @return type
     */
    public function greaterThan($id=false) {
        $results = false;
        if ($id = ($id) ? $id : ($this->getId() ? $this->getId() : false)) {
            $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
            $query  = <<<SQL
                select *
                  from {$table}
                 where id > {$id}
SQL;
            $results = $this->query($query);            
        }
        return $results;
    }
    
    /**
     * Gets rows from table where the ID is greater than or equal to a set amount
     * 
     * @param type $id
     * @return type
     */    
    public function greaterThanOrEqualTo($id=false) {
        $results = false;
        if ($id = ($id) ? $id : ($this->getId() ? $this->getId() : false)) {
            $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
            $query  = <<<SQL
                select *
                  from {$table}
                 where id >= {$id}
SQL;
            $results = $this->query($query);            
        }
        return $results;
    }   
    
    /**
     * Gets rows from table where the ID is less than a set amount
     * 
     * @param type $id
     * @return type
     */    
    public function lessThan($id=false) {
        $results = false;
        if ($id = ($id) ? $id : ($this->getId() ? $this->getId() : false)) {
            $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
            $query = <<<SQL
                select *
                  from {$table}
                 where id < {$id}
SQL;
            $results = $this->query($query);            
        }
        return $results;
    }    

    /**
     * Gets rows from table where the ID is less than or equal to a set amount
     * 
     * @param type $id
     * @return type
     */    
    public function lessThanOrEqualTo($id=false) {
        $results = false;
        if ($id = ($id) ? $id : ($this->getId() ? $this->getId() : false)) {
            $table = $this->_actual() ? $this->_actual() : $this->_prefix().$this->_entity();
            $query = <<<SQL
                select *
                  from {$table}
                 where id <= {$id}
SQL;
            $results = $this->query($query);            
        }
        return $results;
    }    
    
    /**
     * We need to get those fields that are the keys, because they are treated differently than normal columns
     */
    public function loadEntityKeys($useCache=true)  {
        $namespace = $this->_namespace();
        $entity    = $this->_entity();
        $this->_module(Humble::module($namespace));
        $primary   = ($useCache) ? Humble::cache('entity_keys-'.$namespace.'/'.$entity) : false;
        if (!$primary) {
            $query = <<<SQL
                select a.key, a.auto_inc, b.polyglot, b.actual from humble_entity_keys as a
                 inner join humble_entities as b
                    on a.namespace = b.namespace
                   and a.entity = b.entity
                 where a.namespace = '{$namespace}'
                   and a.entity    = '{$entity}'
SQL;
            $primary    = $this->_db->query($query);
            if (count($primary)===0) {
                /*
                 * We haven't found any keys for this table, so it probably means that this table
                 *  is an optional table.  If so, we go to look for a humble table of the same name
                 *  and load that one instead
                 */
                $query = <<<SQL
                    select * from humble_entity_keys as a
                     inner join humble_entities as b
                        on a.namespace  = b.namespace
                       and a.entity     = b.entity
                     where a.namespace  = 'humble'
                       and a.entity     = '{$entity}'
SQL;
                $primary    = $this->_db->query($query);
                if (count($primary)!==0) {
                    $this->_namespace('humble');  //Mark that we got this from humble
                    $this->_prefix('humble_');
                }
            }
            Humble::cache('entity_keys-'.$namespace.'/'.$entity,$primary);
        }
        if (isset($primary[0]['actual']) && $primary[0]['actual']) {
            $this->_actual($primary[0]['actual']);
        }
        $poly = true; //why am I making everything polyglot?
        foreach ($primary as $row => $entity) {
            if ($poly) {
                $this->_polyglot($entity['polyglot']);
            }
            $this->_keys[$entity['key']]    = "";                   //register key
            $this->_autoinc[$entity['key']] = $entity['auto_inc'];  //register auto inc value
        }
    }

    /**
     *
     */
    public function loadEntityColumns($useCache=true) {
        $namespace = $this->_namespace();
        $entity    = $this->_entity();
        $columns   = ($useCache) ? Humble::cache('entity_columns-'.$namespace.'/'.$entity) : false;
        if (!$columns) {
            $query = <<<SQL
                select * from humble_entity_columns
                 where namespace = '{$namespace}'
                   and entity    = '{$entity}'
SQL;
            $columns    = $this->_db->query($query);
            if (count($columns)===0) {
                /*
                 * We haven't found any fields for this table, so it probably means that this table
                 *  is an optional table.  If so, we go to look for a humble table of the same name
                 *  and load that one instead.
                 *
                 * Not sure if this is a good idea yet...
                 */
                $query = <<<SQL
                    select * from humble_entity_columns
                     where namespace = 'humble'
                       and entity    = '{$entity}'
SQL;
                $columns    = $this->_db->query($query);
                if (count($columns)!==0) {
                    $this->_namespace('humble');  //Mark that we got this from humble
                    $this->_prefix('humble_');
                }
            }
            Humble::cache('entity_columns-'.$namespace.'/'.$entity,$columns);
        }
        foreach ($columns as $row => $entity) {
            $this->_column[$entity['column']]    = true;   //register column
        }
    }
    /**
     *
     * @param type $field
     */
    protected function remove($field)  {
        if (isset($this->_fields[$field])) {
            unset($this->_fields[$field]);
        } else if (isset($this->_keys[$field])) {
            unset($this->_keys[$field]);
        }
        return $this;
    }

    /**
     *
     */
    public function lastQuery() {
        return $this->_db->_lastQuery();
    }

    /**
     *
     */
    public function lastError() {
        return $this->_db->_lastError();
    }

    //################################################################################################
    //
    //THE FOLLOWING METHODS ARE CONVENIENCE METHODS FOR ENTITIES IN THE XML CONTROLLERS
    //
    //################################################################################################
    public function nonKeysLoad() {
        return $this->load(true);
    }
    public function useKeysFetch() {
        return $this->fetch(true);
    }
    public function loadNext() {
        $this->load();
        return $this->next();
    }
    public function loadPrevious() {
        $this->load();
        return $this->previous();
    }
    //###############################################################################################
    //                      END OF SPECIAL METHODS FOR XML CONTROLLERS
    //###############################################################################################

    /**
     * For pagination, can set whether to use a cursor or not.  Primarily for use when in a controller
     * 
     * @param bool $cursor
     * @return $this
     */
    public function _cursor($cursor=null) {
        if ($cursor!==null) {
            $this->_cursor = $cursor;
            return $this;
        }
        return $this->_cursor;
    }
    
    /**
     * Gets the 'revision_history' array from the polyglot source
     *
     * @return array
     */
    public function _revisions() {
        $history = [];
        $id      = $this->getId();
        if ($id) {
            $revisions  = clone $this;
            $data       = $revisions->reset()->setId($id)->_polyglot(true)->load();
            $history    = (isset($data['revision_history'])) ? $data['revision_history'] : [];
            if ($this->_page()) {
                $this->_rowCount(count($history));
                $this->addLimit($this->_page());
                if ($this->_toRow() > $this->_rowCount()) {
                    $this->_toRow($this->_rowCount());
                }
                $this->_fromRow($this->_rows() * ($this->_page()-1)+1);
                $this->_headers['pagination'] = json_encode([
                    'rows' => [
                        'from'  => $this->_fromRow(),
                        'to'    => $this->_toRow(),
                        'total' => $this->_rowCount()
                    ],
                    'pages' => [
                        'current' => $this->_page(),
                        'total'   => $this->_pages()
                    ]
                ]);
            }
        }
        return $history;
    }

    /**
     * Calling this will save off the current record in a polyglot history array
     */
    public function _retain() {
        $id = $this->getId();
        if ($id) {
            $history = clone $this;
            $data    = $history->reset()->setId($id)->_polyglot(true)->load();
            if ($data) {
                $revisions  = (isset($data['revision_history'])) ? $data['revision_history'] : [];
                if (isset($data['revision_history'])) {
                    unset($data['revision_history']);
                }
                $user       = Humble::entity('default/user/identification')->setId(Environment::whoAmI())->load();
                $revisions[] = [
                    'date'      => date('Y-m-d H:i:s'),
                    'user_id'   => $_SESSION['login'],
                    'last_name' => $user['last_name'],
                    'first_name' => $user['first_name'],
                    'revision'  => $data
                ];
                $history->setRevisionHistory($revisions);
                $history->save();
            }
        }
    }
    /**
     * Returns the currently set of "magic" variables
     *
     * @return array
     */
    public function _data() {
        return $this->_data;
    }


    /**
     *
     */
    public function _entity($arg=false) {
        if ($arg) {
            $this->_entity = $arg;
            if ($this->_prefix) {
                $this->loadEntityKeys();
                $this->loadEntityColumns();
            }
            return $this;
        } else {
            return $this->_entity;
        }
    }

    /**
     *
     */
    public function _module($arg=false) {
        if ($arg!==false) {
            $this->_module = $arg;
        } else {
            return $this->_module;
        }
        return $this;
    }

    /**
     * Used in conjunction with dynamic for building semi-dynamic where clauses
     */
    public function _alias($alias=null) {
        if ($alias === null) {
            return $this->_alias;
        } else {
            $this->_alias = $alias;
        }
        return $this;
    }
    
    /**
     * Flag to dynamically build the where clause
     */
    public function _dynamic($state=null) {
        if ($state === null) {
            return $this->_dynamic;
        } else {
            $this->_dynamic = $state;
        }
        return $this;
    }
    
    /**
     *
     */
    public function _polyglot($arg=null) {
        if ($arg!==null) {
            $this->_polyglot = ($arg==='Y') ? true : (($arg==="N") ? false : $arg );
        } else {
            return $this->_polyglot;
        }
        return $this;
    }

    /**
     *
     */
    public function _prefix($arg=false) {
        if ($arg) {
            $this->_prefix = $arg;
            if ($this->_entity) {
                $this->loadEntityKeys();
                $this->loadEntityColumns();
            }
        } else {
            return $this->_prefix;
        }
        return $this;
    }

    /**
     *
     */
    public function _distinct($arg=false) {
        if ($arg) {
            $this->_distinct = $arg;
            return $this;
        } else {
            return $this->_distinct ? 'DISTINCT' : '';
        }
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
            $this->_page                = ($arg > 1) ? $arg : 1;
        }
        return $this;
    }

    /**
     *
     */
    public function _rows($arg=false) {
        if ($arg === false) {
            return $this->_rows;
        } else {
            $this->_rows                = $arg;
        }
        return $this;
    }

    /**
     *
     */
    public function _rowCount($arg=false){
        if ($arg === false) {
            return $this->_rowCount;
        } else {
            $this->_rowCount             = $arg;
        }
        return $this;
    }

    /**
     *
     */
    public function _fromRow($arg=false) {
        if ($arg === false) {
            return $this->_fromRow;
        } else {
            $this->_fromRow             = $arg;
        }
        return $this;
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
            $this->_toRow             = $arg;
        }
        return $this;
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
        return $this;
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
        return $this;
    }

    /**
     * Adds the order by clause with a default collation of ascending if not passed one
     * 
     * @param type $field
     * @return $this
     */
    public function _orderBy($field) {
        $fields = explode(",",$field);
        foreach ($fields as $field) {
            $data       = explode('=',$field);
            $direction  = (isset($data[1])) ? $data[1] : ' ASC ';
            $this->_orderBy[$data[0]]   = $direction;
        }
        return $this;
    }

    /**
     * Relay... do I really need this?
     * @param type $field
     */
    public function orderBy($field) {
        $this->_orderBy($field);
        return $this;
    }
    
    /**
     * Should we run the translations against what is returned
     *
     * @param type $arg
     * @return $this
     */
    public function withTranslation($arg=false) {
        $this->_translation = $arg;
        return $this;
    }

    public function _headersSent($arg=null) {
        if ($arg!==null) {
            $this->_headersSent = $arg;
        }
        return $this->_headersSent;
    }

    /**
     * This sets whether to remove MongoDB _id references from the result set.  The default is to do just that
     *
     * @param type $arg
     * @return $this
     */
    public function _clean($arg=null) {
        if ($arg!==null) {
            $this->_clean = $arg;
        } else {
            return $this->_clean;
        }
        return $this;
    }

    /**
     * Whether to normalize the result set so each row has the same number of columns and in the same order
     *
     * @param boolean $arg
     * @return $this
     */
    public function _normalize($arg=null) {
        if ($arg !== null) {
            $this->_normalize = $arg;
        } else {
            return $this->_normalize;
        }
        return $this;
    }    
    
    /**
     * For batching inserts, updates, and deletes, sets the number of statements to collect before executing them
     *
     * @param type $arg
     * @return $this
     */
    public function _batch($arg=null) {
        if ($arg!==null) {
            $this->_batch = ($arg===false) ? false : (int)$arg;
            return $this;
        } else {
            return $this->_batch;
        }
    }

    /**
     * We have to remove the variable from the general data array as well as the fields array
     *
     * @param type $name
     * @return \Code\Framework\Humble\Entities\Unity
     */
    protected function _unset($name=false) {
        if (($name) && isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
        if (($name) && isset($this->_fields[$name])) {
            unset($this->_fields[$name]);
        }
        return $this;
    }
    
    /**
     * Returns the last result as a serialized (JSON) object
     * 
     * @return string
     */
    public function __toString() {
        return $this->_lastResult->__toString();
    }
    
    /**
     * This method overrides the similar method in the humble model object.  We do so because we need to prevent "accidental" RPC behavior
     *
     * @param string $name
     * @param array $arguments
     * @return type
     */
    public function __call($name, $arguments)  {
        if (substr($name,0,3)=='set') {
            $token  = $name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($name,3)));
            if (array_key_exists($token,$this->_keys)) {
                $this->_keys[$token] = $arguments[0]; //keep track of the keys we are using
            } else {
                $this->_fields[$token] = $arguments[0]; //keep track of the fields we are using
            }
            return $this->__set($token,$arguments[0]);
        } elseif (substr($name,0,3)=='get') {
            $name       = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($name,3)));
            $result     = $this->__get($name);
            return $result;
        } elseif (substr($name,0,5)=='unset') {
            $token      = lcfirst(substr($name,5));
            return $this->_unset($token);
        } elseif (substr($name,-2,2)==="In") {
            $this->_inField = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',substr($name,0,strlen($name)-2)));
            $this->in($arguments[0]);
            return $this;
        } elseif (substr($name,-7,7)==="Between") {
            $this->_betweenField = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',substr($name,0,strlen($name)-7)));
            $this->between($arguments);
            return $this;
        }        
        //method couldn't be handled
        $virtual = $this->_isVirtual() ? 'Virtual' : 'Real';
        die("<pre>\nError:\n\nMethod not found: (".$name.") from (".$virtual.')'.$this->getClassName().".\n\n</pre>");
        return null;
    }

}
?>