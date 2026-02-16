<?php
namespace Code\Framework\Humble\Traits;

trait Base {
    
    protected $_data        = [];
    protected $_prefix      = false;
    protected $_namespace   = false;
    protected $_timestamp   = false;
    protected $_isVirtual   = false;
    protected $_isWindows   = false;
    protected $_isLinux     = false;
    protected $_decrypt     = false;
    protected $_encrypt     = false;   
    protected $_iv          = 'Humble Framework';                             //encryption initialization vector      
    
    function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
    /**
     * Confirm or return what type of component this class is
     * 
     * @param string $something
     * @return boolean
     */
    public function is($something=null) {
        if ($something) {
            return (strtolower($something) === $this->whatAmI);
        }
        return $this->whatAmI;
    }
    
    /**
     * Returns true if this is a virtual class (not a physical one)
     * 
     * @TODO: Review This! Since false is a valid value, the logic here might be off
     * 
     * @param type $virtual
     * @return $this
     */
    public function _isVirtual($virtual=false) {
        if ($virtual) {
            $this->_isVirtual = $virtual;
            return $this;
        } else {
            return $this->_isVirtual;
        }
    }
    
    /**
     * Returns the current class name
     *
     * @return string The name of the current class
     */
    public function getClassName() {
        return __CLASS__;
    }
    
    public function _isLinux() {
        return $this->_isLinux;
    }

    public function _isWindows() {
        return $this->_isWindows;
    }    
    
    /**
     * Sets the flag on whether something should be encrypted before being set
     * 
     * @param type $encrypt
     * @return $this
     */    
    public function encrypt($encrypt=false) {
        $this->_encrypt  = $encrypt;
        return $this;
    }
    
    /**
     * Sets the flag on whether something needs to be decrypt before being returned
     * 
     * @param type $decrypt
     * @return $this
     */
    public function decrypt($decrypt=false) {
        $this->_decrypt = $decrypt;
        return $this;
    }
    
    /**
     * Can set the Initialization Vector for SSL encryption/decryption or just return the current value for that vector
     * 
     * @param mixed $vector
     * @return string
     */
    public function iv($vector=false) {
        if ($vector) {
            $this->_iv = $vector;
            return $this;
        }
        return $this->_iv;
    }
    
    /**
     * Returns a timestamp.  If the timestamp isn't set yet, it sets and stores it.
     *
     * @return timestamp The current/saved timestamp
     */
    public function _getTimeStamp() {
        return ($this->_timestamp) ? $this->_timestamp : $this->_timestamp = microtime(true);
    }

    /**
     * Returns an uppercase alpha-numeric for use in things like generating passwords.  Default size is 6 letters, but you can make it any size you'd like
     *
     * @param type $size
     * @return string
     */
    public function _token($size = 6) {
        $token = '';
        $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i=0; $i<$size; $i++) {
            $token .= substr($alpha,rand(0,25),1);
        }
        return $token;
    }

    /**
     * Returns a unique number to use as an ID
     *
     * @params  boolean $moreEntropy If true, returns a 13 digit unique number
     */
    public function _uniqueId($moreEntropy=false) {
        return uniqid($moreEntropy);
    }
    
    /**
     * Removes an element from the data array
     * 
     * @param type $name
     * @return $this
     */
    protected function _unset($name=false) {
        if (($name) && isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
        return $this;
    }
    
    /**
     * Basic magic method for setting, with an option to encrypt the value before setting it.  If encrypted, the flag is disabled after setting
     * 
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name,$value)   {
        if ($this->_encrypt) {
            $value = openssl_encrypt($value,'aes-128-ctr',\Environment::application('serial_number'),0,$this->iv());
            $this->_encrypt = false;
        }
        $this->_data[$name] = $value;
        return $this;
    }
    
    /**
     * Returns a value from a magic method or from a remote resource.
     *
     * This method is called when a method has been invoked that does not exist, however
     * the non-existent method's name began with the convention 'get', thus indicating that
     * you were trying to retrieve something.  If this request can be satisfied by the magic-
     * method array, then that value is returned.  If it can't be satisfied by the magic method,
     * this routine will load a yaml file representing a namespace set of remote resources, and
     * if the label requested matches any label in the yaml file, that yaml will be invoked.
     *
     * @TODO: For when we "jump" namespaces, figure out how to precede mappings with their namespace
     *
     * @param string $name A pnuemonic, label or variable name
     * @return string Variable value or response from remote resource
     */
    public function __get($name)   {
        $retval = null;
        if (!is_array($name)) {
            if (isset($this->_data[$name])) {
                $retval = $this->_data[$name];
                if ($this->_decrypt) {
                    $retval = openssl_decrypt($retval,'aes-128-ctr',\Environment::application('serial_number'),0,$this->iv());
                    $this->_decrypt = false;
                }
            }
        }
        return $retval;
    }

    /**
     * Magic method to handle non-existant methods being invoked
     *
     * Whenever a method is called that doesn't exist, this method traps the name
     * of the method, and any arguments.
     *
     * @param string $name The name of the method
     * @param array $arguments arguments passed to the non-existant method
     */
    public function __call($name, $arguments)  {
        $retval = null;
        if ($token = lcfirst(substr($name,3))) {
            if (substr($name,0,3)=='set') {
                if (!$arguments) {
                    return $this;
                }
                return $this->__set($token,$arguments[0]);
            } elseif (substr($name,0,3)=='get') {
                return $this->__get($token);
            } elseif (substr($name,0,5)=='unset') {
                $token      = lcfirst(substr($name,5));
                return $this->_unset($token);
            } else {
                if ($this->is()==='model') {
                    if (($retval = $this->_remoteProcedureCall($name)) === null) {
                        \Log::console("Undefined Method: ".$name." invoked from ".$this->getClassName().".");
                    }
                    return $retval;
                } else {
                    throw new \Exceptions\MethodNotFound("Method not found: (".$name.") from (".($this->_isVirtual() ? 'Virtual' : 'Real').')'.$this->getClassName(),16);                    
                }
            }
        }
    }
    

    /**
     * Can set or get a timestamp.
     *
     * If you pass in a value, it stores that value as the stamp, otherwise it
     * returns what ever value is currently stored as the stamp
     *
     * @param timestamp $timestamp A timestamp to use
     * @return string The current timestamp
     */
    public function _timestamp($ts=false) {
        if ($ts) {
            $this->_timestamp = $ts;
            return $this;
        } else {
            return $this->_timestamp;
        }
    }

    /**
     * Can set or get namespace being used by the current class.
     *
     * If you pass in a value, it stores that value as the namespace, otherwise it
     * returns what ever value is currently stored as the namespace
     *
     * @param timestamp $arg A namespace to use
     * @return string The current namespace
     */
    public function _namespace($arg=false) {
        if ($arg) {
            $this->_namespace = $arg;
            return $this;
        } else {
            return $this->_namespace;
        }
    }

    /**
     * Can set or get db prefix being used by the current namespace
     *
     * If you pass in a value, it stores that value as the DB prefix, otherwise it
     * returns what ever value is currently stored as the DB prefix
     *
     * @param string $arg A prefix to use
     * @return string The current DB prefix
     */
    public function _prefix($arg=false) {
        if ($arg) {
            $this->_prefix = $arg;
            return $this;
        } else {
            return $this->_prefix;
        }
        return $this;
    }
}