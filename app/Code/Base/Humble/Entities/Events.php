<?php
namespace Code\Base\Core\Entities;
use Humble;
use Log;
use Environment;
/**
 * 
 * Core Event Queries
 *
 * see title
 *
 * PHP version 5.5+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rmyers@argusdentalvision.com
 * @copyright  2005-Present Humble Project
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
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
            select distinct namespace from core_events
              order by namespace
SQL;
        return $this->query($query);
    }
}