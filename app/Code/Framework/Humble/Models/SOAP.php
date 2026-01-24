<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * SOAP related methods
 *
 * A composite class for handling SOAP related stuff
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class SOAP extends Model
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
     * Bakes in a WS-Addressing SOAP Header in a less than desirable way
     *
     * @param type $action
     * @param type $to
     * @param type $replyto
     * @param type $message_id
     * @return \SoapHeader
     */
    public function generateWSAddressingHeader($action,$to,$replyto,$message_id=false,$ns='ns2')
    {
        $message_id = ($message_id) ? $message_id : $this->_uniqueId(true);
        $soap_header = <<<SOAP
            <{$ns}:Action env:mustUnderstand="0">{$action}</{$ns}:Action>
            <{$ns}:MessageID>urn:uuid:{$message_id}</{$ns}:MessageID>
            <{$ns}:ReplyTo>
              <{$ns}:Address>{$replyto}</{$ns}:Address>
            </{$ns}:ReplyTo>
            <{$ns}:To env:mustUnderstand="0">$to</{$ns}:To>
SOAP;
        return new \SoapHeader('http://www.w3.org/2005/08/addressing','Addressing',new \SoapVar($soap_header, XSD_ANYXML),true);
    }

    /**
     * Bakes in a WS-Security SOAP Header in a less than desirable way
     *
     * @param type $username
     * @param type $password
     * @param type $timestamp
     * @return \SoapHeader
     */
    public function generateWSSecurityHeader($username,$password,$timestamp)
    {
        $soap_header = <<<SOAP
            <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                    <wsse:UsernameToken wsu:Id="UsernameToken-10">
                            <wsse:Username>{$username}</wsse:Username>
                            <wsse:Password Type='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText'>{$password}</wsse:Password>
                    </wsse:UsernameToken>
SOAP;
        if ($timestamp) {
            $hs = gmdate('Y-m-d\TH:i:s.u\Z', strtotime(date('Y-m-d H:i:s')) - (120));
            $he = gmdate('Y-m-d\TH:i:s.u\Z', strtotime(date('Y-m-d H:i:s')) + (120));
            $soap_header .= <<<SOAP
                    <wsu:Timestamp wsu:Id="TS-9">
                            <!-- # CURRENT UTC TIME -->
                            <wsu:Created>{$hs}</wsu:Created>
                            <wsu:Expires>{$he}</wsu:Expires>
                    </wsu:Timestamp>
SOAP;
        }
        $soap_header .= '               </wsse:Security>';
        return new \SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd','Security',new \SoapVar($soap_header, XSD_ANYXML),true);
    }
    
    /**
     * Processes the arguments array to remove non name/value pairs.
     *
     * @param array $call Composite array
     *
     * @return array Processed args
     */
    protected function _processSoapArguments($arguments,$ucfirst=false) {
        $args = []; $cast = false;
        foreach ($arguments as $var => $val) {
            if ($p = strpos($var,'|')) {
                $cast = substr($var,$p+1);
                $var  = substr($var,0,$p);
            }
            if ($cast) {
                switch (strtolower($cast)) {
                    case "lc"   :
                        $var = lcfirst($var);
                        break;
                    case "uc"   :
                        $var = ucfirst($var);
                        break;
                    default : break;
                }
            }
            if (is_array($val)) {
                $args = array_merge($args,[$var => $this->_processSoapArguments($val,$ucfirst)]);
            } else {
                $var  = ($ucfirst) ? ucfirst($var) : $var;
                $cast = false;
                if (!is_numeric($var)) {
                    if (trim($val) != '') {
                        $args[$var] = $val;
                    } else {
                        $method = 'get'.underscoreToCamelCase(str_replace('-','_',$var),true);
                        $args[$var] = $this->$method();
                        if ($cast) {
                            switch (strtolower($cast)) {
                                case "int"  :
                                    $args[$var] = (int)$args[$var];
                                    break;
                                case "float"  :
                                    $args[$var] = (float)$args[$var];
                                    break;
                                case "string"  :
                                    $args[$var] = (string)$args[$var];
                                    break;
                                case "bool"  :
                                    $args[$var] = (bool)$args[$var];
                                    break;
                                default     :
                                    break;
                            }
                        }
                    }
                } else {
                    $method = 'get'.underscoreToCamelCase(str_replace('-','_',$var),true);
                    $args[$val] = $this->$method();
                }
            }
        }
        return $args;
    }
    
    /**
     * A handy dandy method to print all that we can about the last SOAP transaction to a specifiable directory
     *
     * @param \SoapClient $client
     * @param string $prefix
     * @param object $result
     */
    public function writeSoapHeaders($client,$prefix='SOAP',$result='') {
        @mkdir('headers',0775,true);
        file_put_contents('headers/'.$prefix.'_last_request.txt',$client->__getLastRequest());
        file_put_contents('headers/'.$prefix.'_last_request_headers.txt',$client->__getLastRequestHeaders());
        file_put_contents('headers/'.$prefix.'_last_response.txt',$client->__getLastResponse());
        file_put_contents('headers/'.$prefix.'_last_response_headers.txt',$client->__getLastResponseHeaders());
        file_put_contents('headers/'.$prefix.'_last_result.txt',$result);
    }    
}