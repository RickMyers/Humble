<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Event Field methods
 *
 * Some generic functions that perform actions on event fields
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Other
 * @author     Rick <rick@humbleprogramming.com>
 */
class Field extends Model
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
     * Adds a certain amount to a field specified by a configuration page
     * 
     * @workflow use(PROCESS) configuration(/workflow/field/addamount)
     * @param type $EVENT
     */
    public function addAmount($EVENT=false) {
        if ($EVENT) {
            $data = $EVENT->load();
            $cnfg = $EVENT->fetch();
            if (isset($data[$cnfg['field']])) {
                $amount = ($cnfg['amount']) ? (int)$cnfg['amount'] : 0;
                $EVENT->update([$data[$cnfg['field']] => ((int)$data[$cnfg['field']]+$amount)]);
            }
        }
    }
}