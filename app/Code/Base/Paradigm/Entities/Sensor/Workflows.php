<?php
namespace Code\Base\Paradigm\Entities\Sensor;
use Humble;
use Log;
use Environment;
/**    
 *
 * Sensor Workflows
 *
 * See Title
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Other
 * @author     Richard Myers rick@humblecoding.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://humblecoding.com/license.txt
 * @version    1.0
 * @link       https://humblecoding.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Workflows extends \Code\Base\Paradigm\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns only those workflows triggered by a detctor that are active
     *
     * @return type
     */
    public function fetchActive() {
        $query = <<<SQL
           SELECT a.*, b.workflow_id
             FROM paradigm_sensors AS a
            INNER JOIN paradigm_sensor_workflows AS b
               ON a.id = b.sensor_id
	        INNER JOIN paradigm_workflows AS c
	           ON b.workflow_id = c.workflow_id
	        WHERE a.active = 'Y'
	          AND c.active = 'Y'
	          AND a.sensor = '{$this->getSensor()}'
SQL;
        return $this->query($query);
    }
}