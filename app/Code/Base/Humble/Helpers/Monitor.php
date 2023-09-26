<?php
namespace Code\Base\Humble\Helpers;
use Humble;
use Singleton;
/**   
 *
 * Sends data to the browser console.  To prevent it from possibly sending data more than once, we are using the Singleton manager to handle the data.
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
class Monitor extends Helper 
{

    /**
     * Constructor
     */
    public function __construct() {
    }

    /**
     * If there were any messages we send them back to the requester in the response header upon instance destruction
     */
    public function __destruct() {
    }    
    
    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

}


