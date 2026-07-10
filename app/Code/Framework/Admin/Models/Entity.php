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
}