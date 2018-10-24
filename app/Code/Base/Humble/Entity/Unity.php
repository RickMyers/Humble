<?php
namespace Code\Base\Humble\Entity;
use Humble;
use Environment;
use Log;
class Unity extends \Code\Base\Humble\Model\BaseObject
{
    protected $_entity        = null;
    protected $_keys          = [];
    protected $_column        = [];
    protected $_fields        = [];
    protected $_orderBy       = [];
    private   $_orderBuilt    = false;
    protected $_fieldList     = "*";
    protected $_groupBy       = [];
    protected $_db            = null;
    protected $_having        = [];
    protected $_search        = [];
    protected $_autoinc       = [];
    protected $_currentPage   = 0;
    protected $_rowCount      = 0;
    protected $_page          = 0;
    protected $_rows          = 0;
    protected $_fromRow       = 0;
    protected $_toRow         = 0;
    protected $_joins         = [];
    protected $_distinct      = false;
    protected $_mongodb       = null;
    protected $_mongocollection = null;
    protected $_mongoJoin     = 'id';
    protected $_polyglot      = null;
    protected $_module        = null;
    protected $_headersSent   = false;
    protected $_headers       = [];
    protected $_clean         = true;   //if polyglot, clean out MongoDB _id references in the result set
    protected $_translation   = false;  //if true, parse result set looking for tokens and replace them with corresponding value from lookup table
    private   $_collections   = [];     //a hash table of connections to be used in a polyglot transaction.  This is used to improve performance
    protected $_batchsql      = [];
    protected $_batch         = false;
    protected $_isVirtual     = false;
    protected $_normalize     = false;

    /**
     * Initial constructor
     *
     * If this is a polyglot transaction, use the $this->query() function since
     * it performs the necessary checks.  Otherwise it is ok to just go against
     * the $this->_db->query() direct DB call, which bypasses mongodb
     *
     */
    public function __construct() {
        parent::__construct();
        $this->_db = Humble::getDatabaseConnection($this);
    }

    /**
     *
     */
    public function __destruct()  {
        //if pagination is set, store the page in the session
        if ($this->_page()) {
            if (!isset($_SESSION['pagination'])) {
                $_SESSION['pagination'] = [];
            }
            if (!isset($_SESSION['pagination'][$this->_namespace()])) {
                $_SESSION['pagination'][$this->_namespace()] = [];
            }
            $_SESSION['pagination'][$this->_namespace()][$this->_entity()] = $this->_currentPage;
            $list = [];
            if (!(php_sapi_name() === 'cli')) {
                foreach ($this->_headers as $header => $val) {
                    header($header.': '.$val);
                    $list[] = $header;
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
    public function normalize($iterator=false) {
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
    public function clean()  {
        $this->_keys        = [];
        $this->_columns     = [];
        $this->_fields      = [];
        $this->_orderBy     = [];
        $this->_groupBy     = [];
        $this->_search      = [];
        $this->_joins       = [];
        $this->_data        = [];
        return $this;
    }

    /**
     *
     */
    public function reset()  {
        $this->clean();
        $this->loadEntityKeys();
        $this->loadEntityColumns();
        return $this;
    }

    /**
     *
     */
    public function distinct($field=false) {
        $retval = [];
        if ($field) {
            $query = <<<SQL
                select distinct {$field} from {$this->_prefix()}{$this->_entity()}
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
                $data  = Humble::getModule($parts[0]);
                $this->_mongodb = $data['mongodb'];
                $this->_mongocollection = $parts[1];
            } else {
                $data  = Humble::getModule($this->_namespace());
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

    /**
     *
     */
    public function describe() {
        $query = <<<SQL
          describe {$this->_prefix()}{$this->_entity()}
SQL;
        return $this->_db->query($query);
    }

    /**
     *
     */
    public function leftJoin($table=false,$field_l=false,$field_r=false) {
        $table = explode('/',$table);
        $success = false;
        if (count($table)===2) {
            $module = Humble::getModule($table[0]);
             if (isset($module['prefix']) && ($module['prefix']!="")) {
                $table = $module['prefix'].$table[1];
                if ($table && $field_l && $field_r) {
                    $success = true;
                    $this->_joins[] = array("table" => $table, "field_l" => $field_l, "field_r" => $field_r);
                }
            }
        }
        return $success;
    }

    /**
     *
     */
    protected function addJoins() {
        $joinQuery = '';
        foreach ($this->_joins as $idx => $data) {
            $joinQuery .= " as L_{$idx} left outer join {$data["table"]} as R_{$idx} on L_{$idx}.{$data["field_l"]} = R_{$idx}.{$data["field_r"]} ";
        }
        return $joinQuery;

    }

    /**
     *
     */
    protected function addLimit($page) {
       $query = '';
       $p = $this->_page();
       if ($this->_rows() && $this->_page() && $page) {
            $this->_fromRow($pre = (($page-1) * $this->_rows()));
            $this->_toRow($page * $this->_rows());
            $query .= ' limit '.$pre.','.$this->_rows();
        }
        return $query;
    }

    /**
     *
     */
    public function _fieldList($list='') {
        if ($list === '') {
            return $this->_fieldList;
        } else {
            $this->_fieldList = $list;
        }
    }

    /**
     *
     */
    public function truncate()  {
        $query = <<<SQL
        truncate table {$this->_prefix}{$this->_entity}
SQL;
        return $this->_db->query($query);
    }

    /**
     *
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
        $query = "select {$group},coalesce(avg({$field}),'0') as `average` from ".$this->_prefix().$this->_entity();
        $results = $this->_db->query($query);
        return $results[0]['average'];
    }

    /**
     *
     */
    public function search($field=false,$text=false)   {
        if (($field !== false) && ($text !== false)) {
            $query = <<<SQL
            select SQL_CALC_FOUND_ROWS * from {$this->_prefix()}{$this->_entity()}
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
            $query   = "select {$countRowClause} * from {$this->_prefix()}{$this->_entity()}";
            $query  .= $this->addJoins();
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
     *
     */
    public function totalRows()  {
        $query = <<<SQL
            select count(*) as total from {$this->_prefix()}{$this->_entity()}
SQL;
        $results = $this->_db->query($query);
        return (count($results) == 1) ? $results[0]["total"] : 0;
    }

    /**
     *
     */
    public function fetchRow($rowNum)  {
        $query = <<<SQL
        select * from {$this->_prefix()}{$this->_entity()}
            limit $rowNum,1
SQL;
        $results = $this->query($query);
        return (count($results) == 1) ? $results[0] : null;
    }

    /**
     *
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
        $query = "select * from ".$this->_prefix().$this->_entity();
        $andFlag = false;
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
            Log::console('Entity error:  No index field found, likely a configuration error, or you should be doing your lookup using non-key fields.  Check the config.xml and make sure the entity is listed in the ORM section ['.$this->_prefix().$this->_entity().']');
            return false; //no field found to index on, so we load nothing
        } else {
            $query .= ' LIMIT 1'; //load returns the first instance to match only
        }
        $results = $this->query($query);
        if ($this->_polyglot() && (count($results->toArray())>0)) {
            //now get the mongo object...
            $mod = $this->_module();
            if (isset($this->_collections[$mod['mongodb'].'/'.$this->_entity()])) {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()]->reset();
            } else {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()] = Humble::getCollection($mod['mongodb'].'/'.$this->_entity());
            }
            $col = $results->toArray()[0];
            $id  = isset($col['id']) ? $col['id'] : (isset($col['uid']) ? $col['uid'] : false);
            if (!$id) {
                die('No id found for the mongo merge');
            }
            $mdb->setId($id);
            $rows = $mdb->load();
            if ($rows) {
                foreach ($rows as $key=>$val) {
                    if (!isset($col[$key])) {
                        $col[$key] = $val;
                    }
                }
            }
        }
        if (count($results->toArray())>0) {
            foreach ($results->toArray()[0] as $field => $value) {
                $method = 'set'.ucfirst($field);
                $this->$method($value);
            }
        }
        $result = (count($results->toArray())>0) ? $results->toArray()[0] : null;
        //now if polyglot and there are extra polyglot fields, go through this and exclude if they don't match
        if ($result && $this->_translation) {
            foreach ($result as $key => $val) {
                $result[$key] = Humble::string($val);
            }
        }
        return $result;
    }

    /**
     *
     */
    protected function buildWhereClause($useKeys) {
        $query = '';
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
                $query .= ($andFlag ? "and `": "`").$field."` = '".addslashes($value)."' ";
                $andFlag = true;
            }
        }
        return $query;
    }

    /**
     *
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
                $query .= $field.' '.$direction;
                $ctr++;
            }
        }
        if ($query) {
            $this->_orderBuilt = true;
        }
        return $query;
    }

    /**
     *
     */
    public function fetch($useKeys = false) {
        if ($this->_page()) {
            if (!isset($_SESSION['pagination'][$this->_namespace()][$this->_entity()])) {
                $this->_currentPage($this->_page());
            } else if ($this->_currentPage() === null) {
                $this->_currentPage($_SESSION['pagination'][$this->_namespace()][$this->_entity()] + 1);
            }
        }
        $query   = "select SQL_CALC_FOUND_ROWS ". $this->_distinct() ." ".$this->_fieldList()." from ".$this->_prefix().$this->_entity();
        $query  .= $this->addJoins();
        $query  .= $this->buildWhereClause($useKeys);
        $query  .= $this->buildOrderByClause();
        if (count($this->_groupBy) > 0) {
            //update query with group by clause
            if (count($this->_having) > 0) {
                //and optionally a having clause, array with three elements
            }
        }

        $query  .= $this->addLimit($this->_currentPage);
        $results = $this->query($query);
        //if (count($results)==0) {
        //    $results = [];
       // }
        return $results;
    }

    /**
     *
     */
    protected function calculateStats(&$results) {
        $query = <<<SQL
         select FOUND_ROWS()
SQL;
        $rows = $this->_db->query($query);
        $this->_rowCount($rows[0]['FOUND_ROWS()']);
        if ($this->_page()) {
            if (count($results) < $this->_rows()) {
                $_SESSION['pagination'][$this->_namespace()][$this->_entity()] = 0;
            }
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
        return $results;
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
     */
    public function save() {
        $mod    = $this->_module();
        $mdb    = false;
        if ($this->_polyglot()) {
            if (isset($this->_collections[$mod['mongodb'].'/'.$this->_entity()])) {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()]->reset();
            } else {
                $mdb = $this->_collections[$mod['mongodb'].'/'.$this->_entity()] = Humble::getCollection($mod['mongodb'].'/'.$this->_entity());
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
        $fieldlist  = '`'.$this->implode_keys('`,`',$db_fields).'`';
        $values = '';
        foreach ($db_fields as $field) {
            $values .= $values ? "," : "";
            $values .= ($field || ($field===0) || ($field==='0')) ? "'".addslashes($field)."'" : "NULL";
        }
        //$values     = "'".implode("','",$db_fields)."'";
        $duplicates = [];
        foreach ($this->_fields as $key => $value) {
            if (isset($this->_column[$key])) {
                $duplicates[$key] = $db_fields[$key];
            }
        }
        $duplicate  = '`';
        foreach ($duplicates as $key => $value) {
            $duplicate .= (($duplicate === '`') ? '' : ",`").$key."` = ";
            $duplicate .= ($value || ($value===0) || ($value==='0')) ? "'".addslashes($value)."'" : "NULL";
        }
        $query      = <<<SQL
            insert into {$this->_prefix()}{$this->_entity()}
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
                $method = 'set'.ucfirst($key);
                $mdb->$method($value);
            }
            //then we save
            $mdb->save();
        }
        return (($insertId) ? $insertId : $this->getId());
    }

    /**
     *
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
        $query = <<<SQL
            insert into {$this->_prefix()}{$this->_entity()}
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
    public function query($query) {
        if (!$this->_orderBuilt && (count($this->_orderBy)>0)) {
            $query .= $this->buildOrderByClause();
        }
        $words  = explode(' ',trim($query));
        if (strtoupper($words[0])==='SELECT') {
            if ($this->_page()) {
                if (strpos(strtoupper($query),'SQL_CALC_FOUND_ROWS')===false) {
                    $words[0] .= ' SQL_CALC_FOUND_ROWS';
                }
                $ctr = 0; $limitFound = false;
                /* I need to look and see if there's already a limit statement AT THE END of the query.
                 * There could be a limit elsewhere, so I am checking to see
                 * if the word LIMIT is present in the last 6 or so words... if so, I don't add a limit
                 */
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
            }
        }
        if ($this->_batch && ($words[0]!=="SELECT")) {                          //for insert, update, and deletes we want to enable batch operations
            $this->_batchsql[] = $query;
            if (count($this->_batchsql) >= $this->_batch) {
                //now go execute the batch sql statements
                $this->_batchsql = []; //reset the sql buffer
            }
            return false;
        }
        $results = $this->_db->query($query);
        //\Log::error($query);
        if ($this->_page()) {
            $this->calculateStats($results);
        }
        if ($this->_polyglot()) {
            //now get the mongo document and merge with the mysql row...
            $mJoin  = $this->_mongoJoin;      //what field in the query will we join with the mongo db, default is 'id' but can be any field in the result set
            if (!$this->_mongodb) {
                $mod = $this->_module();
                $this->_mongodb = $mod['mongodb'];
            }
            $entity = ($this->_mongocollection) ? $this->_mongocollection : $this->_entity();   //If you had used the "with()" function, you'd have set mongodb variable, otherwise use the entity name from the MySQL query
            $mdb    = Humble::getCollection($this->_mongodb.'/'.$entity);
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
                $id     = array('$in'=>$ids);  //Get all documents with those IDs
                $mdb->setId($id);
                $rows   = $mdb->fetch();
                if ($rows) {
                    $int = []; $key = false;
                    foreach ($results as $idx => $row) {
                        if (!$key) {
                            $key = $mJoin;
                        }
                        $id = isset($row[$mJoin]) ? $row[$mJoin] : false;
                        if ($id) {
                            if (!isset($int[$id])) {
                                $int[$id] = [];
                            }
                            $int[$id][] = $idx;
                        }
                    }
                    if (count($int)) {
                        //And this craziness merges the document data with the mysql data, being careful not to overlay mysql field values with mongo field values if they both happen to have the same field
                        foreach ($rows as $row) {
                            if (isset($int[$row['id']])) {
                                foreach ($int[$row['id']] as $index) {
                                    foreach ($row as $var => $val) {
                                        if (!isset($results[$int[$row['id']][$index]][$var])) {
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
        return Humble::getModel('humble/iterator')->clean($this->_polyglot() && $this->_clean())->withTranslation($this->_translation)->set($results);  //is this backwards?
    }

    /**
     *
     * @param type $useFields
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
        $query   = "delete from ".$this->_prefix().$this->_entity();
        $conditionFound = false;
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
        if ($conditionFound) {
            $this->_db->query($query);
            //POLYGLOT check here
            //@TODO: Implement a check to see if this is a polyglot table, and remove corresponding row in MongoDB
        } else {
            Log::console('Ignoring delete since no condition for the delete was found');
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
        if ($id) {
            $this->reset();
            $query  = <<<SQL
                select id
                  from {$this->_prefix()}{$this->_entity()}
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
            $query  = <<<SQL
                select id
                  from {$this->_prefix()}{$this->_entity()}
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
       // $this->_db->commit();
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
        $query    = "select count(*) as count from ".$this->_prefix().$this->_entity();
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


    /**
     * Gets rows from table where the ID is greater than a set amount
     *
     * @param type $id
     * @return type
     */
    public function greaterThan($id=false) {
        $results = false;
        if ($id = ($id) ? $id : ($this->getId() ? $this->getId() : false)) {
            $query  = <<<SQL
                select *
                  from {$this->_prefix()}{$this->_entity()}
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
            $query  = <<<SQL
                select *
                  from {$this->_prefix()}{$this->_entity()}
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
            $query  = <<<SQL
                select *
                  from {$this->_prefix()}{$this->_entity()}
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
            $query  = <<<SQL
                select *
                  from {$this->_prefix()}{$this->_entity()}
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
        $this->_module(Humble::getModule($namespace));
        $primary   = ($useCache) ? Humble::cache('entity_keys-'.$namespace.'/'.$entity) : false;
        if (!$primary) {

            $query = <<<SQL
                select a.key, a.auto_inc, b.polyglot from humble_entity_keys as a
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
                 *  is an optional table.  If so, we go to look for a core table of the same name
                 *  and load that one instead
                 */
                $query = <<<SQL
                    select * from humble_entity_keys as a
                     inner join humble_entities as b
                        on a.namespace  = b.namespace
                       and a.entity     = b.entity
                     where a.namespace  = 'core'
                       and a.entity     = '{$entity}'
SQL;
                $primary    = $this->_db->query($query);
                if (count($primary)!==0) {
                    $this->_namespace('core');  //Mark that we got this from core
                    $this->_prefix('humble_');
                }
            }
            Humble::cache('entity_keys-'.$namespace.'/'.$entity,$primary);
        }
        $poly = true;
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
                 *  is an optional table.  If so, we go to look for a core table of the same name
                 *  and load that one instead.
                 *
                 * Not sure if this is a good idea yet...
                 */
                $query = <<<SQL
                    select * from humble_entity_columns
                     where namespace = 'core'
                       and entity    = '{$entity}'
SQL;
                $columns    = $this->_db->query($query);
                if (count($columns)!==0) {
                    $this->_namespace('core');  //Mark that we got this from core
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
                $user       = Humble::getEntity('humble/user_identification')->setId(Environment::whoAmI())->load();
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
    public function _namespace($arg=false) {
        if ($arg) {
            $this->_namespace = $arg;
        } else {
            return $this->_namespace;
        }
        return $this;
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
     *
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
     *
     */
    public function _groupBy($field)     {   $this->_groupBy[]     = $field;     }

    /**
     * Should we run the translations against what is returned
     *
     * @param type $arg
     * @return \\Code\Base\Humble\Entity\Unity
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
     * @return \\Code\Base\Humble\Entity\Unity
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
     * We don't support the extended RPC functionality in an entity, so we are suppressing that here
     *
     * @param type $name
     * @return type
     */
    public function __get($name)   {
        $retval = null;
        if (!is_array($name)) {
            if (isset($this->_data[$name])) {
                $retval = $this->_data[$name];
            }
        }
        return $retval;
    }

    /**
     * We have to remove the variable from the general data array as well as the fields array
     *
     * @param type $name
     * @return \Code\Base\Humble\Entity\Unity
     */
    protected function _unset($name=false) {
        parent::_unset($name);
        if (($name) && isset($this->_fields[$name])) {
            unset($this->_fields[$name]);
        }
        return $this;
    }

    /**
     * This method overrides the similar method in the core model object.  We do so because we need to prevent "accidental" RPC behavior
     *
     * @param string $name
     * @param array $arguments
     * @return type
     */
    public function __call($name, $arguments)
    {
        $token      = substr($name,3);
        $token{0}   = strtolower($token{0});
        if (substr($name,0,3)=='set') {
            $token  = $name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $token));
            if (array_key_exists($token,$this->_keys)) {
                $this->_keys[$token] = $arguments[0]; //keep track of the keys we are using
            } else {
                $this->_fields[$token] = $arguments[0]; //keep track of the fields we are using
            }
            return $this->__set($token,$arguments[0]);
        } elseif (substr($name,0,3)=='get') {
            $name       = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $token));
            $result     = $this->__get($name);
            return $result;
        } elseif (substr($name,0,5)=='unset') {
            $token      = substr($name,5);
            $token{0}   = strtolower($token{0});
            return $this->_unset($token);
        }
        //method couldn't be handled
        $virtual = $this->_isVirtual() ? 'Real' : 'Virtual';
        die("<pre>\nError:\n\nMethod not found: (".$name.") from (".$virtual.')'.$this->getClassName().".\n\n</pre>");
        return null;
    }

}
?>