<?php
namespace Code\Integration\USPS\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * API Call Manager
 *
 * Manages calling the USPS API 
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Manager extends Model
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
     * Transfers the parameters that were passed into the request builder helper to be used in shaping the XML that is sent to the USPS API
     * 
     * @return object
     */
    private function prepRequestBuilder() {
        $rb = Humble::getHelper('usps/requestBuilder');
        foreach ($this->_data as $field => $value) {
            $method = 'set'.$this->underscoreToCamelCase($field,true);
            $rb->$method($value);
        }
        return $rb;
    }

    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function addressVerify() {
        return $this->setXML(($this->prepRequestBuilder())->verifyAddressRequest())->verifyAddress();
    }

    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function lookupZipCode() {
        return $this->setXML(($this->prepRequestBuilder())->zipcodeLookupRequest())->zipCodeLookup();
    }

    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function lookupCityState() {
        return $this->setXML(($this->prepRequestBuilder())->cityStateLookupRequest())->cityStateLookup();
    }

}