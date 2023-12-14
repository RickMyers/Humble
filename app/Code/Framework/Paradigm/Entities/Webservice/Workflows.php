<?php
namespace Code\Framework\Paradigm\Entities\Webservice;
use Humble;
use Log;
/**    
 *
 * Webservice Workflow custom queries
 *
 * see title...
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
class Workflows extends \Code\Framework\Paradigm\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns only those workflows tied to a webservice that are active
     *
     * @return iterator
     */
    public function fetchActiveWebserviceWorkflows() {
        $query = <<<SQL
            SELECT a.*
              FROM paradigm_webservice_workflows AS a
             INNER JOIN paradigm_workflows AS b
                ON a.workflow_id = b.`workflow_id`
             WHERE b.active = 'Y'
               and uri = '{$this->getUri()}'
SQL;
        return $this->query($query);
    }
}