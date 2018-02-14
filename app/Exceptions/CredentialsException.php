<?php
namespace Exceptions;
/**
 *   _____                 _   _            __ __           _ _
 *  |   __|_ _ ___ ___ ___| |_|_|___ ___   |  |  |___ ___ _| | |___ ___
 *  |   __|_'_|  _| -_| . |  _| | . |   |  |     | .'|   | . | | -_|  _|
 *  |_____|_,_|___|___|  _|_| |_|___|_|_|  |__|__|__,|_|_|___|_|___|_|
 *                    |_|
 *
 * Exception Handling Wrapper
 *
 * Please see the developer documentation for more information on handling exceptions
 *
 * PHP version 7.2+
 *
 * @category   Exceptions
 * @package    Framework
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0.1
 *
 */
class CredentialsException extends \Exception {

    public function __construct($message, $code=0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getClassName() {
        return __CLASS__;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
    public function getFileName() {
        return "";
    }

}
?>