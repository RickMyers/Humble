<?php
namespace Code\Integration\USPS\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * BulkXMLRequestBuilder
 *
 * Generate XML for bulk address verification request
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Core
 * @author     Rick Myers rick@humbleprogramming.com
 */
class BulkRequestBuilder extends Helper
{
    private $id             = 0; //increments everytime a call is made
    private $user_id        = '';

    /**
     * Constructor
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
    public function verifyAddressRequest($addresses) {
        $xml = '<AddressValidateRequest USERID="'.$this->userId().'">';
        foreach($addresses as $address) {
            $xml .= <<<STR
                <Address ID="{$this->id()}">
                    <Address1>{$address["address1"]}</Address1>
                    <Address2>{$address["address2"]}</Address2>
                    <City>{$address["city"]}</City>
                    <State>{$address["state"]}</State>
                    <Zip5>{$address["zipcode"]}</Zip5>
                    <Zip4></Zip4>
                </Address>
STR;            
        }
        $xml .= '</AddressValidateRequest>';
        return $this->inline($xml);
    }
    
    /**
     * Returns the XML required to perform a USPS lookup for city and state of the passed zipcode
     * 
     * @return type
     */
    public function cityStateLookupRequest($addresses) {
        $xml = '<CityStateLookupRequest USERID="'.$this->userId().'">';
        foreach($addresses as $address) {
            $xml .= <<<STR
                <ZipCode ID="{$this->id()}">
                    <Zip5>{$address["zipcode"]}</Zip5>
                </ZipCode>
STR;            
        }
        $xml .= '</CityStateLookupRequest>';

        return $this->inline($xml);
    }
    
    /**
     * Returns the XML for a USPS ZipCode Lookup
     * 
     * @return string
     */
    public function zipcodeLookupRequest($addresses) {
        $xml = '<ZipCodeLookupRequest USERID="'.$this->userId().'">';
        foreach($addresses as $address) {
            $xml .= <<<STR
                <Address ID="{$this->id()}">
                    <Address1>{$address["address1"]}</Address1>
                    <Address2>{$address["address2"]}</Address2>
                    <City>{$address["city"]}</City>
                    <State>{$address["state"]}</State>
                </Address>
STR;            
        }
        $xml .= '</ZipCodeLookupRequest>';

        return $this->inline($xml);
    }
}