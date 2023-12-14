<?php
namespace Code\Framework\Paradigm\Entities\Event;
use \Humble;
use \Environment;
/**
 *
 * Queries related to the event log table
 *
 * see title
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
class Log extends \Code\Framework\Paradigm\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a certain number of rows from the event log table
     *
     * @param boolean $useKeys
     * @return iterator
     */
    public function fetch($useKeys=false) {
        $query = <<<SQL
        SELECT a.mongo_id AS id, a.`event`, a.user_id, date_format(a.modified,'%m/%d/%Y %H:%i:%s') as `date`, b.last_name, b.first_name
          FROM paradigm_event_log AS a
          LEFT OUTER JOIN humble_user_identification AS b
            ON a.user_id = b.id
SQL;
        return $this->query($query);
    }

}