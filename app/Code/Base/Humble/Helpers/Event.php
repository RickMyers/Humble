<?php
namespace Code\Base\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * Event Helpers
 *
 * Methods used to work with events
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@humblecoding.com
 * @copyright  2005-present Enicity.com
 * @license    https://humblecoding.com/license.txt
 * @version    1.0.0
 * @link       https://humblecoding.com/docs/class-Event.html
 * @since      File available since Release 1.0.0
 */
class Event extends Helper
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
     *
     *
     * @param type $text
     * @param type $values
     * @return type
     */
    public function substitute($text,$values) {
        $retval = '';
        foreach (explode('%%',$text) as $idx => $section) {
            if ($idx%2 != 0) {
                if (strpos($section,'.')) {
                    $s = '$values';
                    foreach (explode('.',$section) as $node) {
                        $s .= "['".$node."']";
                    }
                    eval('$valid = isset('.$s.');');
                    if ($valid) {
                        eval('$retval .='.$s.';');                              //Yes, it is evil, but what else are you going to do?
                    } else {
                        $retval .= '';
                    }
                } else {
                    $retval .= isset($values[$section]) ? $values[$section] : '';
                }
            } else {
                $retval .= $section;
            }
        }
        return $retval;
    }

    /**
     * Takes the data element array from an event and a string representation of a field and returns the value of that field within the data array
     *
     * @param array $data
     * @param string $field
     * @return mixed
     */
    public function evaluate($data,$field) {
        $s      = '';
        foreach (explode('.',$field) as $idx => $node) {
            $s .= (!$s) ? '$data['.$node.']' : "['".$node."']";
        }
        eval('$a='.$s.';');
        return $a;
    }
}