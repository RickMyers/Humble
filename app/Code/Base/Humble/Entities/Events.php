<?php
namespace Code\Base\Humble\Entities;
use Humble;
use Log;
use Environment;
/**
 *
 * .then( Event Queries
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rick@humblecoding.com.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://humblecoding.com/license.txt
 * @version    1.0
 * @link       https://humblecoding.com/docs/class-&&MODULE&&.html
 * @since      File available since Release 1.0.0
 */
class Events extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function uniqueNamespaces() {
        $query = <<<SQL
            select distinct namespace from paradigm_events
              order by namespace
SQL;
        return $this->query($query);
    }
}