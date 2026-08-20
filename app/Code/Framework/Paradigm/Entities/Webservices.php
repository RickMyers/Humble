<?php
namespace Code\Framework\Paradigm\Entities;
use Humble;
use Log;
use Environment;
/**
 *
 * Paradigm Webservices queries
 *
 * see description
 *
 * PHP version 7.0+
 *
 * @category   Entity
 * @package    Workflow Editor
 * @author     Rick rick@humbleprogramming.com
 */
class Webservices extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function list($useKeys=false) {
        $query = <<<SQL
            SELECT * 
              FROM paradigm_webservices AS a
              LEFT OUTER JOIN paradigm_webservice_workflows AS b
                ON a.id = b.webservice_id
               AND a.`active` = 'Y'
              LEFT OUTER JOIN paradigm_workflows AS c
                ON b.workflow_id = c.id            
               AND c.`active` = 'Y'
SQL;
        return $this->query($query);
    }
}