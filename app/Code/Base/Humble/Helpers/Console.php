<?php
namespace Code\Base\Humble\Helpers;
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
class Console 
{

    private $messages = [];
    private $errors   = [];
    private $warnings = [];
    private $alerts   = [];
    
    /**
     * Constructor
     */
    public function __construct() {
    }

    /**
     * If there were any messages we send them back to the requester in the response header
     */
    public function __destruct() {
        $list = "";
        if (!(php_sapi_name() === 'cli')) {
            foreach ($this->errors as $error) {
                $list .= (($list)?",":"").'"'.addslashes($error).'"';
            }
            if ($list) {
                header('Errors: ['.$list.']');
            }
            $list = "";
            foreach ($this->warnings as $warning) {
                $list .= (($list)?",":"").'"'.addslashes($warning).'"';
            }
            if ($list) {
                header('Warnings: ['.$list.']');
            }
            $list = "";
            foreach ($this->messages as $message) {
                $list .= (($list)?",":"").'"'.addslashes($message).'"';
            }
            if ($list) {
                header('Messages: ['.$list.']');
            }
            foreach ($this->alerts as $alert) {
                $list .= (($list)?",":"").'"'.addslashes($alert).'"';
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
        if ($message) {
            $this->messages[] = $message;
        }
        return $this;
    }

    /**
     * Stores an error message
     * 
     * @param string $message
     * @return $this
     */
    public function error($message=false) {
        if ($message) {
            $this->errors[] = $message;
        }
        return $this;
    }
    
    /**
     * Stores a warning message
     * 
     * @param string $message
     * @return $this
     */
    public function warn($message=false) {
        if ($message) {
            $this->warnings[] = $message;
        }
        return $this;        
    }
}

