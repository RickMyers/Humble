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
class Console 
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
        $list = "";
        if (!(php_sapi_name() === 'cli')) { 
            foreach (Singleton::log() as $message) {
                $list .= (($list)?",":"").'"'.addslashes(str_replace(['\n','\r'],['',''],$message)).'"';
            }

            if ($list) {
                header('Messages: ['.$list.']');
            }
            $list = '';
            foreach (Singleton::error() as $error) {
                $list .= (($list)?",":"").'"'.addslashes(str_replace(['\n','\r'],['',''],$error)).'"';
            }
            if ($list) {
                header('Errors: ['.$list.']');
            }
            $list = "";
            foreach (Singleton::warn() as $warning) {
                $list .= (($list)?",":"").'"'.addslashes(str_replace(['\n','\r'],['',''],$warning)).'"';
            }
            if ($list) {
                header('Warnings: ['.$list.']');
            }
            $list = "";
            foreach (Singleton::alert() as $alert) {
                $list .= (($list)?",":"").'"'.addslashes(str_replace(['\n','\r'],['',''],$alert)).'"';
            }
            if ($list) {
                header('Alerts: ['.$list.']');
            }
        }
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
     * Stores a normal message
     * 
     * @param string $message
     * @return $this
     */
    public function log($message=false) {
        Singleton::log($message);
        return $this;
    }

    /**
     * Stores an error message
     * 
     * @param string $message
     * @return $this
     */
    public function error($message=false) {
        Singleton::error($message);
        return $this;
    }
    
    /**
     * Stores a warning message
     * 
     * @param string $message
     * @return $this
     */
    public function warn($message=false) {
        Singleton::warn($message);
        return $this;        
    }
    
    /**
     * Stores a message meant for an alert prompt
     * 
     * @param string $message
     * @return $this
     */
    public function alert($message=false) {
        Singleton::alert($message);
        return $this;
    }      
}

