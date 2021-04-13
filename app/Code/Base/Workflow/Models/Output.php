<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Output Components
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
class Output extends Model
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
     * @workflow use(process) configuration(workflow/output/field)
     * @param type $EVENT
     */
    public function outputField($EVENT=false) {
        if ($EVENT) {
            $data = $EVENT->load();
            $cfg  = $EVENT->fetch();
            if (isset($cfg['field']) && $cfg['field']) {
                $field = isset($data[$cfg['field']]) ? $data[$cfg['field']] : '';
                if (isset($cfg['format']) && $cfg['format']) {
                    $field = ($cfg['format']=='JSON') ? json_encode($field) : $field;
                }
                print($field);
            }
        }
    }
}