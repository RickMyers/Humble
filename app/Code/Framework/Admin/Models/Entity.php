<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Entity App Queries
 *
 * See Title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Entity extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;
	
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function className() {
        return __CLASS__;
    }

    /**
     * Dynamically runs one or more queries, on behalf of the Entity Explorer
     * 
     * @param type $query
     * @return type
     */
    public function run($query=false) {
        $results = [];
        if ($query = ($query) ? $query : $this->getQuery()) {
            $entity = Humble::entity('humble/entity');
            $rows   = $this->getRows();
            $page   = $this->getPage();
            foreach (explode(';',$query) as $idx => $qry) {
                if ($rows || $page) {
                    $entity->rows($rows)->page($page);
                }
                $results['results_'.$idx] = $entity->_normalize(true)->query($qry);
            }
        }
        return $results;
    }
    
    /**
     * Will stage a call to a dynamically allocated entity
     * 
     * @param bool $field
     * @return mixed
     */
    public function load($field=false) {
        $empty     = json_encode([]);
        $namespace = $this->getNamespace();
        $id        = $this->getId();
        $entity    = $this->getEntity();
        $result    = Humble::entity($namespace.'/'.$entity)->setId($id)->load();
        return ($result) ? $result : $empty;
    }
    
    /**
     * Dynamically updates a single row in a table/entity based on a passed in JSON object
     * 
     * @return int
     */
    public function updateRow() {
        $row_id    = null;
        $payload   = json_decode($this->getData());
        $namespace = ($payload['ee_namespace'] ?? false);
        $entity    = ($payload['ee_entity']    ?? false);
        $id        = ($payload['id']           ?? false);
        if ($namespace && $entity && $id) {
            unset($payload['ee_namespace']);
            unset($payload['ee_entity']);
            unset($payload['id']);            
            $e = Humble::entity($namespace.'/'.$entity)->setId($id);
            foreach ($payload as $field => $val) {
                $setter = 'set'.$this->underscoreToCamelCase($field,true);
                $e->$setter($val);
            }
           // $row_id = $e->save();
        }
        return $row_id;
    }
}