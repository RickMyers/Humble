<?php
namespace Base\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**   
 *
 * Date related helper functions
 *
 * Some useful functions for managing dates
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Other
 * @author     Richard Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0.1
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Date extends Helper
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
     * Returns an array of dates, one month apart, based upon initially passed in dates
     *
     * @param date $d1
     * @param date $d2
     * @return array
     */
    public function monthsBetween($d1=false,$d2=false) {
        $months = [];
        if ($d1 && $d2 && !(($d1=='0000-00-00') || ($d2=='0000-00-00'))) {
            $d1         = strtotime($d1);
            $d2         = strtotime($d2);
            $min_date   = min($d1, $d2);
            $max_date   = max($d1, $d2);
            $months[]   = date('m/d/Y',$min_date);
            while (($min_date = strtotime("+1 MONTH", $min_date)) < $max_date) {
                $months[] = date('m/d/Y',$min_date);
            }
        }
        return $months;
    }
}