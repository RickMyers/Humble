<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Environment;
use Log;
/*use Symfony\Component\Yaml\Yaml;*/                                            ///not any more
/**
 *
 * At a minimum, your custom classes should override the getClassName method...
 *
 */
interface HumbleComponent {
    public function getClassName();
    public function load($field=false);
    public function fetch();
}

/**
 * This is the central object from which all other objects ultimately extend
 *
 * The main class of the Humble Framework, it enforces the conventions as defined.
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Original Author <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0.1
 */
class Model implements HumbleComponent
{

    use \Code\Framework\Humble\Traits\Base;  
    
    protected           $_data          = [];
    protected static    $_mappings      = null;
    protected           $_modules       = false;
    protected           $_farmServer    = '';
    protected           $_mongoServer   = '';
    protected           $_iv            = 'Humble Framework';                   //Default initialization vector, see 'Securing Your Application' video for ways to improve this
    protected           $_RPC           = true;                                 // Enhanced "smart-endpoint" feature is turned on 
    protected           $_DEBUG         = false;
    protected           $_REPORT        = [];
    protected           $whatAmI        = 'model';
    
    public function __construct()    {
        $this->_isWindows = (strncasecmp(PHP_OS, 'WIN', 3) === 0);
        $this->_isLinux   = !$this->_isWindows;
    }

    /**
     * Returns just one element of the model if passed in, or nothing null if not passed in or not present in model
     * 
     * @param type $field
     * @return type
     */
    public function load($field=false) {
        $result = null;
        if ($field) {
            $result = isset($this->_data[$field]) ? $this->_data[$field] : null;
        }
        return $result;
    }

    /**
     * Returns the current model, wrapped in the custom Humble Array class
     * 
     * @return array
     */
    public function fetch() {
        return Humble::array($this->_data);
    }
    
    /**
     * Cute routine to convert the next letter after an underscore to uppercase while removing the underscore
     *
     * @param string $string
     * @param boolean $first_char_caps
     * @return string
     */
    public function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
     
    /**
     * Cute routine to insert an underscore prior to any capitalized letter
     *
     * @param string $string
     * @param boolean $make_lower_case
     * @return string
     */
    public function camelCaseToUnderscore($string='',$make_lower_case=true) {
        return $make_lower_case ? strtolower(trim(preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $string))): trim(preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $string));
    }
    
    /**
     * Returns a unique identifier to help identify request threads when you come from a client who might have the site open in multiple tabs
     * 
     * @return string
     */
    public function browserTabId() {
         if (!isset($_SESSION['BROWSER_TABS'])) {
            $_SESSION['BROWSER_TABS'] = [];
        }
        $_SESSION['BROWSER_TABS'][$tab_id = $this->_uniqueId()] = '';
        return $tab_id;
    }
    
    /**
     * Used to foil cross-site request forgeries.   A combination of the tab_id token and the csrf token will be used to make sure the request is kosher
     * 
     * @param string $tab_id
     * @return string
     */
    public function csrfBuster($tab_id) {
        if (!isset($_SESSION['BROWSER_TABS'])) {
            $_SESSION['BROWSER_TABS'] = [];
        }        
        return $_SESSION['BROWSER_TABS'][$tab_id] = $this->_uniqueId();
    }
    
    /**
     * Will check to see if it is possible to send an email
     * 
     * @return bool
     */
    public function isSMTPEnabled() {
        $enabled = false;
        
        return $enabled;
    }
    
    /**
     * Adds arbitrary information to the debug report
     * 
     * @param type $data
     * @return $this
     */
    private function addToDebugReport($data=[]) {
        $this->_REPORT = array_merge($this->_REPORT,$data);
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
     * For use with template substitution
     * 
     * @param string $text
     * @param array $values
     * @return string
     */
    public function substitute($text,$values) {
        $retval = '';
        foreach (explode('%%',$text) as $idx => $section) {
            if ($idx%2 != 0) {
                if (strpos($section,'.')) {
                    $s = '$values';
                    foreach (explode('.',$section) as $node) {
                        $s .= "['".$node."']";
                    }
                    eval('$valid = isset('.$s.');');
                    if ($valid) {
                        eval('$retval .='.$s.';');                              //Yes, it is evil, but what else are you going to do?
                    } else {
                        $retval .= '';
                    }
                } else {
                    $retval .= isset($values[$section]) ? $values[$section] : '';
                }
            } else {
                $retval .= $section;
            }
        }
        return $retval;
    }
    
    /**
     * Everything is set...
     *
     * @param type $name
     * @return boolean
     */
    public function __isset($name=false)    {
        return true;
    }

    /**
     * Call this when what you really want to do is nothing at all...
     */
    public function IEFBR14() {
        //Don't delete this!
    }

    /**
     * Checks to see if the UID variable is set in the session and that it has a value
     *
     * @return boolean
     */
    public function isLoggedIn() {
        return (isset($_SESSION['uid']) && $_SESSION['uid']);
    }

    /**
     * When you want to suppress the enhanced "get" functionality where we try to map a "getted" request to something configured in the yaml files, set this to false.  Default behavior is to try to do a Remote Procedure Call
     *
     * @param boolean $arg
     * @return boolean
     */
    public function _RPC($arg=null) {
        if ($arg !== null) {
            $this->_RPC = $arg;
            return $this;
        }
        return $this->_RPC;
    }

    /**
     * Handy-dandy method for converting a JSON string into an XML string
     * 
     * @param type $xml
     * @param type $elements
     * @return type
     */
    private function arrayToXML($xml,$elements) {
        foreach ($elements as $key => $value) {
            if (is_int($key)) {
                $key = 'Element'.$key;  //To avoid numeric tags like <0></0>
            }
            if (is_array($value)) {
                $xml->addChild($key);
                $xml = arrayToXML($xml, $value);  //Adds nested elements.
            } else {
                $xml->addChild($key, $value);
            }
        }
        return $xml;
        
    }
    /**
     * Will change from one format to another
     * 
     * To allow a custom transformer in the future we should support the following syntax:
     * 
     * Future Format
     *    
     *    transform : 
     *       class  : myHelperClass
     *       method : myTransformMethod
     * 
     * @param string $transformer
     * @param string $result
     * @return string
     */
    private function transformResult($transformer=false,$result=false) {
        if ($transformer && $result) {
            switch (strtoupper($transformer)) {
                case 'XML2JSON' :
                    $result = json_encode(simplexml_load_string($result));
                    break;
                case 'JSON2XML' :
                    if ($json = json_decode($result,true)) {
                        $result = $this->arrayToXML($json,new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>'));
                    }
                    break;
                case 'JSON' :
                    $result = json_decode($result,true);
                    break;
                case 'XML'  :
                    $result = simplexml_load_string($result);
                    break;
                 default         :
                    break;
            }
        }
        return $result;
    }
    /**
     * Constructs a CURL call to access a REST style resource.
     *
     * This method is called with 2 to 5 arguments to access a remote resource. The
     * shape of the URL can activate different ways of handling the variables.  A '+'
     * at the end of the URL means that the arguments will be appended to the end as URI
     * parameters.
     *
     * @param string $URL  The URL of the resource
     * @param array $args  Name/Value pairs that are passed to remote resource
     * @param string $method The HTTP method to use, default is post
     * @param string $userid To conduct a basic validation
     * @param string $password To conduct a basic validation
     * @return string The body of the response from the remote resource
     */
    protected function _curl($call,$args,$secure=false,$userid=false,$password=false)	{
        $URL            = $this->substitute($call['url'],array_merge($_REQUEST,$args));
        $api_var        = (isset($call['api-var']) && $call['api-var']) ? $call['api-var'] : false;
        $api_key        = (isset($call['api-key']) && $call['api-key']) ? $call['api-key'] : false;        
        $method         = isset($call['method']) ? strtoupper($call['method']) : 'POST';
        $res            = null; $opts = []; $parms = '';
        $auth           = ($userid && $password) ? array("Authorization"=> ["Basic" => base64_encode($userid.":".$password)]) : [];
        $protocol       = ($secure) ? 'ssl' : 'http';
        $sessionControl = isset($this->_data['sessionId']) || ((isset($call['blocking']) && (!$call['blocking'])));  //do I need to suspend the current session to give access to the session during the remote call

        if ($sessionControl) {
            $SID = session_id();
            $args['humble_session_id'] = $SID;
            $this->setSessionId($SID);
            session_write_close();
        }
        
        //--> USE HURL INSTEAD... 
        $isHttps = isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS'])==='ON' ? true :
                   (isset($_SERVER['REQUEST_SCHEME']) && strtoupper($_SERVER['REQUEST_SCHEME']==='HTTPS') ? true : 
                   (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])? true : false));        
        if (substr($URL,0,4)!=='http') {
            $URL = $_SERVER['HTTP_HOST'].$URL;
            $URL = ($isHttps) ? 'https://'.$URL : 'http://'.$URL;
        }

        if ($method == "GET") {
            $hurl = $URL.'?'.http_build_query($args,'','&');
        } else {
            $hurl = $URL;
        }

        $ch = curl_init($hurl);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16");

        
        if ($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            if (isset($call['format']) && (strtoupper($call['format'])=='JSON')) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args,'','&'));
            }

        }

        if ($userid && $password){
                // HTTP Basic Auth

                //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_USERPWD, $userid.":".$password);
        }

        if ($secure) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_CAINFO, "/etc/pki/tls/certs/ca-bundle.crt");
            //curl_setopt($ch, CURLOPT_CAPATH, "/etc/pki/tls/certs/");
            //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'ECDHE-RSA-AES256-GCM-SHA384');
            //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'rsa_rc4_128_sha');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

       // $verbose = fopen('php://temp', 'rw+');
        //curl_setopt($ch, CURLOPT_STDERR, $verbose);
        /*maybe?*/
//      curl_setopt($ch, CURLOPT_FAILONERROR, true);
//      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $res 	= curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('CURL Error: '.curl_error($ch));
        }        
    	$info 	= curl_getinfo($ch);

        if ($sessionControl) {
            session_id($SID);
            session_start();
        }

        return isset($call['transform']) ? $this->transformResult($call['transform'],substr($res, $info['header_size'])) : substr($res, $info['header_size']);
    }

    /**
     * Hurl is an HTTP based CURL, using standard PHP stream functions with context wrappers
     *
     * @param string $URL
     * @param array $args
     * @param boolean $secure
     * @param string $method
     * @param string $userid
     * @param string $password
     * @return string
     */
    protected function _hurl($URL,$args,$call,$secure=false,$userid=false,$password=false)	{
        $URL            = $this->substitute($call['url'],array_merge($_REQUEST,$args));
        $api_var        = (isset($call['api-var']) && $call['api-var']) ? $call['api-var'] : false;
        $api_key        = (isset($call['api-key']) && $call['api-key']) ? $call['api-key'] : false;        
        $method         = isset($call['method']) ? strtoupper($call['method']) : 'POST';
        $res            = null; $opts = []; $parms = '';
        $auth           = ($userid && $password) ? array("Authorization"=> ["Basic" => base64_encode($userid.":".$password)]) : [];
        $protocol       = ($secure) ? 'ssl' : 'http';
        $sessionControl = isset($call['blocking']) && ($call['blocking']===false);  //do I need to suspend the current session to give access to the session during the remote call
        $sessionControl = $sessionControl || (substr($URL,0,1)=='/');
        $SID            = false;

        if ($sessionControl) {
            $SID = session_id();
            $args['humble_session_id'] = session_id();
            session_write_close();
         }
        //if you are going to "eat your own dogfood", we need to precede the resource URL with the fully qualified host name
        $HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1';
        /*
         * Below was a workaround for when a model tried to "eat their own dogfood" in the form of an RPC service
         * It would do the call on the external docker port, when it needed to use the internal one
         * I resolved this by changing the vhost to listen on both ports (external and internal), but I am leaving
         *   this here in case the problem arises again. -RGM 
         */
        /*if (isset($_SERVER['DOCKER_PORT_XREF'])) {
            $docker_ports = explode(':',$_SERVER['DOCKER_PORT_XREF']);
            $host_parts = explode(':',$HTTP_HOST);
            if ($docker_ports[1] && $host_parts[1]) {
                $HTTP_HOST = $host_parts[0].':'.$docker_ports[1];
            }
        }*/
        
        if (substr($URL,0,4)!=='http') {
            $isHttps = isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS'])==='ON' ? true :
                       (isset($_SERVER['REQUEST_SCHEME']) && strtoupper($_SERVER['REQUEST_SCHEME']==='HTTPS') ? true : 
                       (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])? true : false));
            if ($isHttps) {            
                $URL      = 'https://'.$HTTP_HOST.$URL;
                $protocol = 'ssl';
            } else {
                $URL      = 'http://'.$HTTP_HOST.$URL;
                $protocol = 'http';
            }
        }
        if ($this->_DEBUG) {
            $this->addToDebugReport([
                'URL' => $URL, 'authorization' => $auth, 'protocol' => $protocol, 'session_control' => $sessionControl
            ]);
        }

        if ($api_var && $api_key) {
            $args[$api_var] = $api_key;
        }
        /*
         * We need to process "PUT"
         *
         * If Put, we still need to handle HTTPS and HTTP
         */
        if (isset($call['suppress'])) {
            foreach ($call['suppress'] as $arg) {
                if (isset($args[$arg])) {
                    unset($args[$arg]);
                }
            }
        }
        $content      = ((isset($call['format']) && (strtoupper($call['format'])) == 'JSON') || (strtoupper($method) === 'PUT')) ? json_encode($args) : http_build_query($args,'','&') ;
        $content_type = "Content-Type: application/x-www-form-urlencoded";
        if (isset($call['format'])) {
            switch (strtoupper($call['format'])) {
                case 'JSON'     :
                    $content_type   = 'Content-Type: application/json';
                    break;
                default         :
                    break;
            }
        }
        if (($method == 'POST') || ($method == 'PUT')) {
            $opts = [];
            switch ($protocol) {
                case "ssl"  :   $opts['ssl'] = [
                                    "verify_peer"=>false,
                                    "verify_peer_name"=>false,
                                    "crypto_method" => STREAM_CRYPTO_METHOD_ANY_SERVER
                                ];
                case "http" :   $opts['http'] = [
                                    'header' => $content_type,
                                    "method" => $method,
                                    'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16", /*whats a little spoofage between friends? */
                                    'Content-Length' => strlen($content),
                                    'content' => $content
                                ];
                                break;
                case "ftp"  :
                                break;
            }
            $opts = array_merge($opts,$auth);
        } else {
            $parms = ($content) ? '?'.$content : '';
        }

        $context = stream_context_create($opts);
        $hurl    = $URL.$parms;
        $fp      = fopen($hurl, 'rb', false, $context);
        if ($this->_DEBUG) {
            $this->addToDebugReport([
                'context' => $opts,
                'complete_URL' => $hurl
            ]);
        }
        
        if ($fp) {
            stream_set_timeout($fp,60000);
            $res = stream_get_contents($fp);
            if ($this->_DEBUG) {
                $this->addToDebugReport([
                    'response' => $res
                ]);
            }
        } else {
            \Log::error("Unable to connect: ".$URL."\nArguments:\n".print_r($args,true));
        }

        if ($sessionControl && $SID) {
            session_id($SID);
            session_start();
        }
        if ($this->_DEBUG) {
            Log::general($this->_REPORT);
        }
        return isset($call['transform']) ? $this->transformResult($call['transform'],$res) : $res;
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
    private function generateWSAddressingHeader($action,$to,$replyto,$message_id=false,$ns='ns2')
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
    private function generateWSSecurityHeader($username,$password,$timestamp)
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
                        $method = 'get'.ucfirst(underscoreToCamelCase(str_replace('-','_',$var)));
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
                    $method = 'get'.ucfirst(underscoreToCamelCase(str_replace('-','_',$var)));
                    $args[$val] = $this->$method();
                }
            }
        }
        return $args;
    }

    /* //just saving this madness for posterity sake
        $file       = $module['resources_templates'].'/'.$template;
        $count      = 0; $max = count($exts); $found = false;
        while ((!$found) && ($count<$max)) {
            $check_file = $file.(($exts[$count]) ? $exts[$count] : '.'.$exts[$count]);
            if ($found = $found || file_exists($check_file)) {
                $found = $check_file;
            }
            $count++;
        }
     */
    
    /**
     * You've been passed as a resource, this tries to see if the resource exists and then return it
     * 
     * @param type $template
     * @return type
     */
    private function fetchTemplateResource($template) {
        $len        = count($parts = explode('/',$template));
        $namespace  = ($len==2) ? $parts[0] : $this->_namespace();
        $template   = ($len==2) ? $parts[1] : $template;
        $module     = Humble::module($namespace);
        $found      = false;
        $file       = false;
        $dh         = dir($dir = 'Code/'.$module['package'].'/'.$module['resources_templates']);        
        while ((($entry = $dh->read()) !== false) && (!$found)) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            
            $found  = file_exists($file = $dir.'/'.$entry);
        }
        return ($found) ? file_get_contents($file) : '';
    }
    
    /**
     * This is me just being a d*ck...
     * 
     * @param type $template
     * @param type $args
     * @return type
     */
    private function templateSubstitution($template='',$args=[]) {
        if (strtolower(substr($template,0,5))==='rs://') {
            $template = $this->fetchTemplateResource(substr($template,5));
        }
        if (($len = count($parts = explode('%%',$template)))>1) {
            $template = '';
            for ($i=0; $i<$len; $i++){
                $template .= ($i%2 == 0) ? $parts[$i] : (isset($args[$parts[$i]]) ? $args[$parts[$i]] : '');
            }
        }
        return $template;
    }
    
    /**
     * Processes the arguments array to remove non name/value pairs.
     *
     * @param array $call Composite array
     *
     * @return array Processed args
     */
    protected function _processArguments($call) {
        $args = [];
        $rpc  = $this->_RPC();  //capture current RPC state
        $this->_RPC(false);     //turn off RPC or might fall into an infinite loop
        if (isset($call['arguments']) && $call['arguments']) {
            foreach ($call['arguments'] as $var => $val) {
                if (!is_numeric($var)) {
                    if ($val && (trim($val) != '')) {
                        $args[$var] = $val;
                    } else {
                        $method = 'get'.$this->underscoreToCamelCase($var,true);
                        $args[$var] = $this->$method();
                    }
                } else {
                    $method = 'get'.$this->underscoreToCamelCase($val,true);
                    $args[$val] = $this->$method();
                }
            }
        }
        if (isset($call['templates'])) {
            foreach ($args as $var => $val) {
                if (isset($call['templates'][$var])) {
                    $args[$var] = $this->templateSubstitution($call['templates'][$var],$args);
                }
            }
        }
        $this->_RPC($rpc);  //restore RPC state
        return $args;
    }

    /**
     * A handy dandy method to print all that we can about the last SOAP transaction to a specifiable directory
     *
     * @param \SoapClient $client
     * @param string $prefix
     * @param object $result
     */
    private function writeSoapHeaders($client,$prefix='SOAP',$result='') {
        @mkdir('headers',0775,true);
        file_put_contents('headers/'.$prefix.'_last_request.txt',$client->__getLastRequest());
        file_put_contents('headers/'.$prefix.'_last_request_headers.txt',$client->__getLastRequestHeaders());
        file_put_contents('headers/'.$prefix.'_last_response.txt',$client->__getLastResponse());
        file_put_contents('headers/'.$prefix.'_last_response_headers.txt',$client->__getLastResponseHeaders());
        file_put_contents('headers/'.$prefix.'_last_result.txt',$result);
    }

    /**
     * Gets values from one object and sets them in this object based on an array list
     * 
     * @param object $object
     * @param array $relationship
     * @return $this
     */
    public function _map($object,$relationship=[]) {
        if ($relationship) {
            foreach ($relationship as $field) {
                $getter = 'get'.$this->underscoreToCamelCase($field,true);
                $setter = 'set'.$this->underscoreToCamelCase($field,true);
                $this->$setter($object->$getter());
            }
        }
        return $this;
    }

    /**
     * Cache a value, first pre-process the expiration value if passed
     * 
     * @param type $namespace
     * @param type $call_name
     * @param type $arguments
     * @param type $expire
     * @param type $results
     * @return type
     */
    protected function pushToCache($namespace=false,$call_name=false,$arguments=[],$expire=0,$results='') {
        ksort($arguments);
        array_map('mb_strtoupper',$arguments);
        if ($expire) {
            $unit = substr($expire,-1,1);
            $base = substr($expire,0,strlen($expire)-1);
            switch (strtoupper($unit)) {
                case 'S' :
                    $expire = $base;
                    break;
                case 'I' :
                    $expire = $base * 60;
                    break;
                case 'H' :
                    $expire = $base * 3600;
                    break;
                case 'D' :
                    $expire = $base * 86400;
                    break;
                case 'W' :
                    $expire = $base * 604800;
                    break;
                case 'M' :
                    $expire = $base * 2592000;
                    break;
                case 'Y' : 
                    $expire = $base * 31536000;
                    break;
                default:
                    $expire = 0;
                    break;
             }
             $expire = time()+$expire;          //creates an actual unix date/time for it to expire rather than an offset
        }
        return Humble::cache($namespace.'-'.$call_name.'-'.md5(http_build_query($arguments)),$results,$expire);
    }
    
    /**
     * Attempts to get a value from the cache
     * 
     * @param type $namespace
     * @param type $call_name
     * @param type $arguments
     * @return type
     */
    protected function pullFromCache($namespace=false,$call_name=false,$arguments=[]) {
        ksort($arguments);
        array_map('mb_strtoupper',$arguments);
        $res = Humble::cache($namespace.'-'.$call_name.'-'.md5(http_build_query($arguments)));
        return $res;
    }
    
    /**
     * If any call parameter value is marked as a secret, we attempt to retrieve that secret here and substitute the value
     * 
     * @param array $call
     * @return array
     */
    protected function processSecrets($call=[]) {
        $manager = false;
        foreach ($call as $key => $val) {
            if (is_array($val)) {
                $call[$key] = $this->processSecrets($val);
            } else {
                if ($val && strtolower(substr($val,0,5))==='sm://') {
                    $manager    = ($manager) ? $manager : Humble::entity('humble/secrets/manager');  //speed up call by only instantiating the orm if a secret is found
                    $len        = count($parts = explode('/',$secret = substr($val,5)));
                    $namespace  = ($len==2) ? $parts[0] : $this->_namespace();
                    $secret     = ($len==2) ? $parts[1] : $secret;
                    if ($x = $manager->reset()->setSecretName($secret)->setNamespace($namespace)->load(true)) {
                        $call[$key] = $manager->decrypt(true)->getSecretValue();
                    }
                }
            }
        }
        return $call;
    }

    /**
     * We are going to allow alternative values to be specified by putting the [ENVIRONMENT] the option is valid in
     * 
     * @param array $call
     * @return array
     */
    protected function processEnvironmentSpecificOptions($call=[]) {
        $options     = [];
        foreach ($call as $option => $value) {
            if (strpos($option,'[')) {
                $opt            = trim(($parts = explode('[',$option))[0]);
                $target         = trim(strtoupper((explode(']',$parts[1]))[0]));
                $options[$opt]  = isset($options[$opt]) ? $options[$opt] : [];
                $options[$opt][$target] = $value;
                unset($call[$option]);                                          //We will need to remove this row and then figure out what goes here in the next routine
            }
        }
        if (count($options)) {
            $environment    = Environment::state();
            foreach ($options as $opt => $variant) {
                if (isset($variant[$environment])) {
                    $call[$opt] = $options[$opt][$environment];
                } else if (isset($variant['DEFAULT'])) {
                    $call[$opt] = $options[$opt]['DEFAULT'];
                }
            }
        }
        return $call;
    }

    
    /**
     * Identifies if a call is available and pre-processes the call options
     * 
     * @param type $name
     * @return varied
     */
    protected function _remoteProcedureCall($name=false) {
        $retval = null;                                                         //Will only return a null if the RPC is not found
        if ($name && $this->_RPC()) {
            if (!\Singleton::mappings()) {
               // if (!$default_mappings = Humble::cache('yaml-humble')) {
               //     Humble::cache('yaml-humble',$default_mappings = yaml_parse(file_get_contents('Code/Framework/Humble/RPC/mapping.yaml')));
               // }
                \Singleton::mappings(yaml_parse(file_get_contents('Code/Framework/Humble/RPC/mapping.yaml'))); //default mappings
            }
            if (strtolower($this->_namespace()) !== 'humble') {
                if ($me = Humble::module($this->_namespace())) {
                    $mappingFile = 'Code/'.$me['package'].'/'.str_replace('_','/',$me['rpc_mapping']).'/mapping.yaml';
                    if (file_exists($mappingFile)) {
                        //In one line, if we already have mappings files, we merge them with the existing set of mappings, otherwise we initialize the mappings to the current mappings
                        //@TODO: cache this
                        if ($map      = yaml_parse(file_get_contents($mappingFile))) {
                            \Singleton::mappings(((\Singleton::mappings()) ? array_merge(\Singleton::mappings(),$map) : $map));
                        } else {
                            print("Problem parsing YaML file ".$mappingFile."\n\nPlease make sure it exists and that it is in correct format.\n");
                        }
                    }
                }
            }
            if (isset(\Singleton::mappings()[$name])) {
                $retval  = false;                                               //RPC call found, so we set default return value to false
                $call    = $this->processSecrets($this->processEnvironmentSpecificOptions(\Singleton::mappings()[$name]));
                if ($this->_DEBUG = (isset($call['DEBUG']) && ($call['DEBUG']===true))) {
                    $this->addToDebugReport([
                        'call' => [
                            'original' => \Singleton::mappings()[$name],
                            'processed' => $call
                        ]
                    ]);
                }
                $args    = [];
                if (isset($call['authentication'])) {
                    switch (strtoupper($call['authentication'])) {
                        case "OAUTH"    :
                                            break;
                        case "OAUTH2"   :
                                            break;
                        case "BASIC"    :
                                            break;
                        default         :   break;
                    }
                } else {
                    if (isset($call['method']) && (strtoupper($call['method'])!=='SOAP')) {
                        if (is_string($call['arguments']) && (substr($call['url'],strlen($call['url'])-1,1)=='+')) {  //this is special handling for those APIs that tack the arguments onto the end of the URI, like dictionary.com does
                            $method      = 'get'.ucfirst($call['arguments']);
                            $call['url'] = substr($call['url'],0,strlen($call['url'])-1).'/'.$this->$method();
                        } else {
                            $args        = array_merge($args,$this->_processArguments($call));
                        }
                        $userid = (isset($call['userid'])   && $call['userid'])     ? $call['userid']   : false;
                        $passwd = (isset($call['password']) && $call['password'])   ? $call['password'] : false;
                        $secure = (isset($call['secure'])   && $call['secure'])     ? $call['secure']   : false;
                        $expire = 0;
                        $engine = false;
                        $cache  = false;
                        if (isset($call['cache'])) {
                            if (is_string($call['cache'])) {
                                $expire = $call['cache'];
                            } else {
                                $expire = isset($call['cache']['expire']) ? $call['cache']['expire'] : 0;
                                if ($engine = isset($call['cache']['engine']) ? $call['cache']['engine'] : 0) {
                                    Humble::cacheEngine($engine);
                                }
                            }
                            if ($retval = $this->pullFromCache($this->_namespace(),$name,$args)) {
                                return $retval;                                 //this ain't the way m8
                            }
                        }
                        $retval = (isset($call['CURL']) && ($call['CURL'])) ? $this->_curl($call,$args,$secure,$userid,$passwd) : $this->_hurl($call['url'],$args,$call,$secure,$userid,$passwd);
                        if (isset($call['cache'])) {
                            $this->pushToCache($this->_namespace(),$name,$args,$call['cache'],$retval);
                        }
                        $retval = ($retval === null) ? false : $retval;         //Only return null when the call wasn't found in the mapping file, otherwise return false
                    } else {
                        //lather it up...
                        $secure     = (isset($call['secure'])   && $call['secure'])     ? $call['secure']    : false;
                        $wsdl       = (isset($call['wsdl'])     && $call['wsdl'])       ? $call['wsdl']      : false;
                        $url        = (isset($call['url'])      && $call['url'])        ? $call['url']       : false;
                        $version    = (isset($call['version'])  && $call['version'])    ? (($call['version']=='1.1') ? SOAP_1_1 : SOAP_1_2) : false;
                        $stamp      = (isset($call['timestamp']) && $call['timestamp']) ? $call['timestamp'] : false;
                        $username   = (isset($call['username']) && $call['username'])   ? $call['username'] : false;
                        $password   = (isset($call['password']) && $call['password'])   ? $call['password'] : false;
                        $wss        = (isset($call['WSS'])      && $call['WSS']);
                        //generateWSSecurityHeader
                        $options = ['cache_wsdl'=>WSDL_CACHE_NONE, 'trace'=>1, 'debug'=> 1, 'exceptions'=>0];
                        if ($url) {
                            $options['location']    =   $url;
                        }
                        if ($version) {
                            $options['soap_version']    =   $version;
                        }
                        if (isset($call['username']) && ($call['username'])) {
                            $options['username'] = $call['username'];
                        }
                        if ((isset($call['password']) && $call['password'])) {
                            $options['password'] = $call['password'];
                        }
                        //
                        $headers = [];
                        if (isset($call['method']) && (strtoupper($call['method']==='SOAP'))) {
                            if (isset($call['client']) && ($call['client'])) {
                                $client = Humble::getClass($call['client'].'/SoapClient')->init($wsdl,$options);
                            } else {
                                $client = new \SoapClient($wsdl,$options);
                            }
                            if ($wss) {
                                $headers[] = $this->generateWSSecurityHeader($username,$password,$stamp);
                            }
                            if (isset($call['header']) && count($call['header'])) {
                                foreach ($call['header'] as $hdr => $hdropts) {
                                    $understand = (isset($hdropts['understand']) && $hdropts['understand']);
                                    $headers[] = new \SoapHeader($hdropts['namespace'],
                                        $hdr,
                                        $hdropts['value'],
                                        $understand);
                                }
                            }
                            if (isset($call['ws-addressing'])) {
                                $ns = (isset($call['ws-addressing']['Namespace']) && $call['ws-addressing']['Namespace']) ? $call['ws-addressing']['Namespace'] : 'ns2';
                                $headers[] = $this->generateWSAddressingHeader($call['ws-addressing']['Action'],$call['ws-addressing']['To'],$call['ws-addressing']['ReplyTo'],false,$ns);
                            }
                            if (count($headers)) {
                                $client->__setSoapHeaders($headers);
                            }

                            $args   = $this->_processSoapArguments($call['arguments'],(isset($call['uc-first']) && $call['uc-first']));

                            if (isset($call['url']) && $call['url']) {
                                $retval = $client->__soapCall($call['operation'],$args);
                            } else {
                                $retval = $client->{$call['operation']}($args);
                            }
                            //uncomment this next line to help in debugging
                            //$this->writeSoapHeaders($client,$call['operation'],$retval);
                        }
                    }
                }
            }
        }
        return $retval;
    }

    /**
     * Checks to see if if a particular module is enabled
     *
     * @TODO: Change this from hitting DB to hitting the cache since module data is cached there already
     * @param array $module
     * @return boolean If module is enabled
     */
    public function isAvailable($module) {
        $ok = true;
        if (isset($this->_modules[$module['type']])) {
            $ok =  ($this->_modules[$module['type']]['enabled'] == 'Y');
        } else {
            $ref = Humble::entity('humble/modules');
            if (isset($module['type'])) {
                $ref->setNamespace($module['type']);
                $mod = $this->_modules[$module['type']]= $ref->load();
                if (isset($mod['enabled'])) {
                    $ok =  ($mod['enabled']=='Y');
                }
            }
        }
        return $ok;
    }


}