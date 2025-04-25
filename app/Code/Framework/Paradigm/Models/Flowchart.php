<?php
namespace Code\Framework\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * Default Methods
 *
 * General purpose stages for workflow construction
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Flowchart extends Model
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
     * Extracts a compound value of an array (field.sub.sub becomes $field['sub']['sub'])
     * 
     * @param string $field
     * @param array $data
     * @return string
     */
    private function parseField($field,$data) {
        $int = false;
        foreach (explode('.',$field) as $part) {
            $int = $int ? $int[$part] : $data[$part];
        }
        return $int;
    }
    
    /**
     * A general purpose if symbol that can be configured to evaluate values in the triggering event action
     *
     * @workflow use(decision) configuration(/workflow/flowchart/if)
     * @param type $EVENT
     * @return type
     */
    public function ifSymbol($EVENT=false) {
        $outcome = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnfg = $EVENT->fetch();
            if (isset($cnfg['field'])) {
                $field = $this->parseField($cnfg['field'],$data);
                switch ($cnfg['operator']) {
                    case "=="   :
                        $outcome = ($field == $cnfg['value']);
                        break;
                    case "==="   :
                        $outcome = ($field === $cnfg['value']);
                        break;
                    case ">="   :
                        $outcome = ($field >= $cnfg['value']);
                        break;
                    case "<="   :
                        $outcome = ($field <= $cnfg['value']);
                        break;
                    case ">"   :
                        $outcome = ($field > $cnfg['value']);
                        break;
                    case "<"   :
                        $outcome = ($field < $cnfg['value']);
                        break;
                    case "!="   :
                        $outcome = ($field != $cnfg['value']);
                        break;
                    default :
                        $EVENT->error('Unsupported operator type of '.$cnfg['operator']);
                        break;
                }
            }
        }
        return $outcome;
    }
}