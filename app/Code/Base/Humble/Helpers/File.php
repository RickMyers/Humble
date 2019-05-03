<?php
namespace Code\Base\Humble\Helpers;
/**
 * 
 * File Handler
 *
 * This class is intended to abstract file handling from the framework.
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Humble_Helpers_File.html
 * @since      File available since Version 1.0.1
 */
class File extends Helper
{

    private $_file      = "";           //Name of file
    private $_fh        = null;         //File Handler
    protected $_error     = "";           //Last Error

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
     * Sets the name of the file to connect to AND establishes the connection
     *
     * @param type $filename
     * @return type
     */
    public function set($filename = false) {
        $this->_fh = false;
        if ($filename) {
            if (file_exists($filename)) {
                $this->_file = $filename;
                $this->_fh = fopen($this->_file,'r+');
            } else {
                $this->_error('File not found [CWD: '.getcwd().'] [FILE: '.$filename.']');
            }
        } else {
            $this->_error('Filename was not passed');
        }
        return $this->_fh;
    }

    /**
     * Returns the entire contents of a file
     *
     * @return type
     */
    public function read($filename=false) {
        if ($this->_fh || $filename) {
            return file_get_contents((($filename) ? $filename : $this->_file));
        }
    }

    /**
     * Reads one line from the file, advancing the file cursor
     *
     * @return string
     */
    public function readLine() {
        if ($this->_fh) {
            return "";
        }
    }

    /**
     * Writes either what was passed in to the file, or what is in the private data array
     *
     * @param type $data
     * @return boolean
     */
    public function write($data=false) {
        if ($this->_fh) {
            return file_put_contents($this->_file,(($data) ? $data : $this->_data));
        }
        return false;
    }

    /**
     * Appends data to the end of a file, such as a log file
     *
     * @param type $data
     * @return boolean
     */
    public function append($data) {
        if ($this->_fh) {
            return file_put_contents($this->_file,$data,FILE_APPEND);
        }
    }

    /**
     * Appends data to the front of a file, such as a log file.  Can't prepend to a file that hasn't been created.
     *
     * @param type $data
     * @return boolean
     */
    public function prepend($message=false) {
        $status = false;
        if ($this->_fh && $message) {
            $status     = true;
            $len        = strlen($message);
            $final_len  = filesize($file) + $len;
            $original   = fread($this->_fh, $len);
            rewind($this->_fh); //does this need to go before first read?
            $i = 1;
            while (ftell($this->_fh) < $final_len) {
                fwrite($this->_fh, $message);
                $message = $original;
                $original = fread($this->_fh, $len);
                fseek($this->_fh, $i * $len);
                $i++;
            }
        }
        return $status;
    }

    /**
     * Returns a certain number of bytes from the beginning of a file
     *
     * @param type $bytes
     * @return string
     */
    public function head($bytes=10000) {
        return "";
    }

    /**
     * Returns a certain number of bytes from the end of a file
     *
     * @param type $bytes
     * @return string
     */
    public function tail($bytes=10000) {
        return "";
    }

    /**
     * Returns most recent error string
     *
     * @return type
     */
    public function _error($message=false) {
        if ($message) {
            $this->_error = $message;
        }
        return $this->_error;
    }

    /**
     * This is useful for templates... returns if a file exists
     *
     * @param string $file
     * @return boolean
     */
    public function exists($file=false) {
        return ($file) ? file_exists($file) : false;
    }
}