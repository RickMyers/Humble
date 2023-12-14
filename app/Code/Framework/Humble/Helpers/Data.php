<?php
namespace Code\Framework\Humble\Helpers;
use Humble;
/**   
 *
 * General data helper
 *
 * Some useful functions for managing data
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Other
 * @author     Richard Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0.1
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Data extends Helper
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
     * Used for progress indicators
     * 
     * @return int
     */
    public function progress() {
        $process = $this->getProcess();
        return json_encode(["progress" => ((isset($_SESSION[$process])) ? $_SESSION[$process] : -1)]);
    }
}
