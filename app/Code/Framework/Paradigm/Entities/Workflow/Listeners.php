<?php
namespace Code\Framework\Paradigm\Entities\Workflow;
use Humble;
/**    
 *
 * Adhoc Queries related to workflow listeners
 *
 * Complex queries around workflow listeners
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Workflow
 * @author     Richard Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Listeners extends \Code\Framework\Paradigm\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a list of workflows that are eligible to be executed by their status
     *
     * @return iterator
     */
    public function active() {
        $query = <<<SQL
            select a.id, a.workflow_id, a.namespace, a.component, a.method, b.active
              from paradigm_workflow_listeners as a
              left outer join paradigm_workflows as b
                on a.workflow_id = b.workflow_id
            where a.namespace = '{$this->getNamespace()}'
              and a.component = '{$this->getComponent()}'
              and a.method    = '{$this->getMethod()}'
              and b.active    = 'Y'
SQL;
        return $this->query($query);
    }

}