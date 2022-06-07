<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Input values to event
 *
 * see description
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2005-present Humble
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Output.html
 * @since      File available since Release 1.0.0
 */
class Input extends Model
{

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
    public function getClassName() {
        return __CLASS__;
    }
    
    /**
     * Outputs a field or fields from the event with optional type of output
     * 
     * @workflow use(process) configuration(workflow/input/field)
     * @param type $EVENT
     */
    public function inputField($EVENT=false) {
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cfg  = $EVENT->fetch();
            if (isset($cfg['field']) && $cfg['field']) {
                if (isset($cfg['field']) && $cfg['field']) {
                    $value = isset($data[$cfg['field']]) ? $data[$cfg['field']] : '';
                    $EVENT->update([$field=>$value]);
                }
            }
        }
    }
}
