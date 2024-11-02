<?php
namespace Code\Integration\USPS\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Call & Cache Manager
 *
 * Manages the USPS API using a cache
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Integration
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Bulk extends Model
{

    private $throttleTimeout    = 2;  //measured in seconds
    private $address            = [
        'list' => [],
        'current' => []
    ];
    
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
    // private function prepRequestBuilder() {
    //     $rb = Humble::getHelper('usps/requestBuilder');
    //     foreach ($this->_data as $field => $value) {
    //         $method = 'set'.$this->underscoreToCamelCase($field,true);
    //         $rb->$method($value);
    //     }
    //     return $rb;
    // }
    private function prepRequestBuilder() {
        $rb = Humble::getHelper('usps/bulkRequestBuilder');
        foreach ($this->_data as $field => $value) {
            $method = 'set'.$this->underscoreToCamelCase($field,true);
            $rb->$method($value);
        }
        return $rb;
    }

    /**
     * Builds the id, which is an MD5 token, based on all segments of the address, even if they weren't passed in
     * 
     * @return string
     */
    protected function buildCacheId($id='') {
        $id .= ','.$this->getAddress1() ?? '';
        $id .= ','.$this->getAddress2() ?? '';
        $id .= ','.$this->getCity() ?? '';
        $id .= ','.$this->getState() ?? '';
        $id .= ','.$this->getZipcode() ?? '';
        return MD5(strtoupper($id));
    }

    /**
     * Attempts to return a cached version of an address
     * 
     * @param type $id
     * @return type
     */
    protected function checkCache($id) {
        //$result = Humble::getEntity('Humble/cache')->setCacheId($id)->load(true);
        $result = false;
        $this->address['list'][$this->address['current']]['cache'] = $result ? 'Y' : 'N';
        return $result ? $result['cache'] : null;
    }
    
    /**
     * Caches the response from USPS along with an MD5 token of the address
     * 
     * @param type $id
     * @param type $value
     * @return type
     */
    protected function setCache($id=false,$value=false) {
        //This doesn't work because we are not caching stuff anymore...
        /*if ($id && $value) {
            Humble::getEntity('Humble/cache')->setCacheId($id)->setCache($value)->save();
        }*/
        return $value;
    }
    
    public function bulkAddressVerify() {
        $helper = Humble::getHelper('usps/BulkRequestBuilder');
        $xml = $helper->verifyAddressRequest($this->getAddresses());
        return $this->setXML($xml)->verifyAddress();
    }
    
    public function bulkLookupZipCode() {
        $helper = Humble::getHelper('usps/BulkRequestBuilder');
        $xml = $helper->zipcodeLookupRequest($this->getAddresses());
        return $this->setXML($xml)->zipCodeLookup();
    }
    
    public function bulkLookupCityState() {
        $helper = Humble::getHelper('usps/BulkRequestBuilder');
        $xml = $helper->cityStateLookupRequest($this->getAddresses());
        return $this->setXML($xml)->cityStateLookup();
    }
    
    public function throttle($results=false) {
        sleep($this->throttleTimeout);
        return $results;
    }
    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function addressVerify() {
        return $result = $this->checkCache($id = $this->buildCacheId('bulk-verify')) ? $result : $this->throttle($this->setCache($id,$this->setXML(($this->prepRequestBuilder())->verifyAddressRequest())->verifyAddress()));
    }

    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function lookupZipCode() {
        return ($result = $this->checkCache($id = $this->buildCacheId('bulk-zipcode'))) ? $result : $this->throttle($this->setCache($id,$this->setXML(($this->prepRequestBuilder())->zipcodeLookupRequest())->zipCodeLookup()));        
    }

    /**
     * Relay for the USPS API
     * 
     * @return XML
     */
    public function lookupCityState() {
        return $result = $this->checkCache($id = $this->buildCacheId('bulk-citystate')) ? $result : $this->throttle($this->setCache($id,$this->setXML(($this->prepRequestBuilder())->cityStateLookupRequest())->cityStateLookup()));                
    }    
}