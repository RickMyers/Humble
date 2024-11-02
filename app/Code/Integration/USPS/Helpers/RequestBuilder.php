<?php
namespace Code\Integration\USPS\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * USPS API Request Builder
 *
 * This helper will assist in the building of XML request objects for the
 * USPS Shipping, Packing, and Address API
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@humbleprogramming.com
 */
class RequestBuilder extends Helper
{

    private $id             = 0; //increments everytime a call is made
    private $user_id        = '';
    
    /**
     * Constructor, and we are going to get the secret for USPS authentication
     */
    public function __construct() {
        $auth = Humble::getEntity('humble/secrets/manager')->setSecretName('USPSUserName');
        $auth->load(true);
        $this->user_id = $auth->decrypt(true)->getSecretValue();
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
     * Returns the un-encrypted user name
     * 
     * @return string
     */
    public function userId() {
        return $this->user_id;
    }
    
    /**
     * Every time a call is made, this counter increments
     * 
     * @return int
     */
    public function id() {
        return $this->id++;
    }
    
    /**
     * Puts a multi-line XML block into a single line (in kind of a jerkish way)
     * 
     * @param type $xml
     * @return type
     */
    public function inline($xml='') {
        if ($xml) {
            foreach (explode("\n",$xml) as $idx => $line) {
                $xml = ((!$idx) ? '' : $xml).trim($line);
            }
        }
        return $xml;
    }
    
    /**
     * This is the XML format to request address verification, filling in those values that I don't pass
     * 
     * @return string
     */
    public function verifyAddressRequest() {
        return $this->inline(<<<XML
            <AddressValidateRequest USERID="{$this->userId()}">
                <Revision>1</Revision>
                <Address ID="{$this->id()}">
                    <Address1>{$this->getAddress1()}</Address1>
                    <Address2>{$this->getAddress2()}</Address2>
                    <City>{$this->getCity()}</City>
                    <State>{$this->getState()}</State>
                    <Zip5>{$this->getZipcode()}</Zip5>
                    <Zip4></Zip4>
                </Address>
            </AddressValidateRequest>                
XML);
    }
    
    /**
     * Returns the XML required to perform a USPS lookup for city and state of the passed zipcode
     * 
     * @return type
     */
    public function cityStateLookupRequest() {
        return $this->inline(<<<XML
            <CityStateLookupRequest USERID="{$this->userId()}">
                <ZipCode ID="{$this->id()}">
                    <Zip5>{$this->getZipcode()}</Zip5>
                </ZipCode>
            </CityStateLookupRequest>
XML);
    }
    
    /**
     * Returns the XML for a USPS ZipCode Lookup
     * 
     * @return string
     */
    public function zipcodeLookupRequest() {
        return $this->inline(<<<XML
            <ZipCodeLookupRequest USERID="{$this->userId()}">
                <Address ID="{$this->id()}">
                    <Address1>{$this->getAddress1()}</Address1>
                    <Address2>{$this->getAddress2()}</Address2>
                    <City>{$this->getCity()}</City>
                    <State>{$this->getState()}</State>
                    <Zip5>{$this->getZipcode()}</Zip5>
                    <Zip4></Zip4>
                </Address>
            </ZipCodeLookupRequest>            
XML);
    }
}