<?php
namespace Code\Base\Humble\Models;
use Humble;
use Environment;
use Log;
/*use Symfony\Component\Yaml\Yaml;*/
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
 * PHP version 5.6+
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

    protected           $_data          = [];
    protected static    $_mappings      = null;
    protected           $_timestamp     = false;
    protected           $_modules       = false;
    protected           $_farmServer    = '';
    protected           $_mongoServer   = '';
    protected           $_prefix        = null;
    protected           $_namespace     = null;
    protected           $_errors        = [];
    protected           $_warnings      = [];
    protected           $_notices       = [];
    protected           $_messages      = [];
    protected           $_isVirtual     = false;
    protected           $_isWindows     = false;
    protected           $_isLinux       = false;
    protected           $_RPC           = true;   /* Enhanced "get" feature is turned on */

    public function __construct()    {
        $this->_isWindows = (strncasecmp(PHP_OS, 'WIN', 3) === 0);
        $this->_isLinux   = !$this->_isWindows;
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
     * Returns true if this is a virtual class (not a physical one)
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
     * Will add to the response headers any messages or errors generated during processing
     */
    public function __destruct() {
        $list = "";
        if (!(php_sapi_name() === 'cli')) {
            foreach ($this->_errors() as $error) {
                $list .= (($list)?",":"").'"'.addslashes($error).'"';
            }
            if ($list) {
                header('Errors: ['.$list.']');
            }
            $list = "";
            foreach ($this->_warnings() as $warning) {
                $list .= (($list)?",":"").'"'.addslashes($warning).'"';
            }
            if ($list) {
                header('Warnings: ['.$list.']');
            }
            $list = "";
            foreach ($this->_notices() as $notice) {
                $list .= (($list)?",":"").'"'.addslashes($notice).'"';
            }
            if ($list) {
                header('Notices: ['.$list.']');
            }
            $list = "";
            foreach ($this->_messages() as $message) {
                $list .= (($list)?",":"").'"'.addslashes($message).'"';
            }
            if ($list) {
                header('Messages: ['.$list.']');
            }
        }
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
     * @return $boolean
     */
    public function _RPC($arg=null) {
        if ($arg !== null) {
            $this->_RPC = $arg;
            return $this;
        }
        return $this->_RPC;
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
	protected function _curl($URL,$args,$method="POST",$secure=false,$userid=false,$password=false)	{
            
        //--> USE HURL INSTEAD... 
        if (substr($URL,0,4)!=='http') {
            $URL = $_SERVER['HTTP_HOST'].$URL;
            $URL = ($this->_isWindows) ? 'http://'.$URL : 'https://'.$URL;
        }

        if ($method == "GET") {
            $hurl = $URL.'?'.http_build_query($args,'','&');
        } else {
            $hurl = $URL;
        }
        $sessionControl = isset($this->_data['sessionId']) || ((isset($call['blocking']) && (!$call['blocking'])));
        if ($sessionControl) {
            $SID = session_id();
            $this->setSessionId($SID);
            session_write_close();
        }
        $ch = curl_init($hurl);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16");

        if ($method == "POST"){
                // send via POST instead of GET
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args,'','&'));
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
    	$info 	= curl_getinfo($ch);

        if ($sessionControl) {
            session_id($SID);
            session_start();
        }
	return substr($res, $info['header_size']);
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
        $URL            = $this->substitute($URL,$args);
        $api_var        = (isset($call['api-var']) && $call['api-var']) ? $call['api-var'] : false;
        $api_key        = (isset($call['api-key']) && $call['api-key']) ? $call['api-key'] : false;        
        $method         = isset($call['method']) ? strtoupper($call['method']) : 'POST';
        $res            = null; $opts = []; $parms = '';
        $auth           = ($userid && $password) ? array("Authorization"=> "Basic ".base64_encode($userid.":".$password)) : [];
        $protocol       = ($secure) ? 'ssl' : 'http';
        $sessionControl = isset($this->_data['sessionId']) || ((isset($call['blocking']) && (!$call['blocking'])));  //do I need to suspend the current session to give access to the session during the remote call

        if ($sessionControl) {
            $args['sessionId'] = session_id();
            session_write_close();
         }
        //if you are going to "eat your own dogfood", we need to precede the resource URL with the fully qualified host name
        $HTTP_HOST = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
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
        if ($api_var && $api_key) {
            //if ($method=='GET') {
            //    $URL .= $api_key.'='.$api_var;
            //} else {
                $args[$api_var] = $api_key;
            //}
        }
        /*
         * We need to process "PUT"
         *
         * If Put, we still need to handle HTTPS and HTTP
         */
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
        stream_set_timeout($fp,60000);

        if ($fp) {
            $res = stream_get_contents($fp);
        } else {
            \Log::error("Unable to connect: ".$URL."\nArguments:\n".print_r($args,true));
        }

        if ($sessionControl) {
            session_id($args['sessionId']);
            session_start();
        }
	return $res;
    }

    /**
     * Bakes in a WS-Addressing SOAP Header in a less than desireable way
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
        $cc   = isset($call['camel-case']) && $call['camel-case'];
        if (isset($call['arguments']) && $call['arguments']) {
            foreach ($call['arguments'] as $var => $val) {
                if (!is_numeric($var)) {
                    if (trim($val) != '') {
                        $args[$var] = $val;
                    } else {
                        $method = 'get'.(($cc) ? $this->underscoreToCamelCase($var,true) : ucfirst($var));
                        $args[$var] = $this->$method();
                    }
                } else {
                    $method = 'get'.(($cc) ? $this->underscoreToCamelCase($val,true) : ucfirst($val));
                    $args[$val] = $this->$method();
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
     * Returns a value from a magic method or from a remote resource.
     *
     * This method is called when a method has been invoked that does not exist, however
     * the non-existent method's name began with the convention 'get', thus indicating that
     * you were trying to retrieve something.  If this request can be satisfied by the magic-
     * method array, then that value is returned.  If it can't be satisfied by the magic method,
     * this routine will load a yaml file representing a namespace set of remote resources, and
     * if the label requested matches any label in the yaml file, that yaml will be invoked.
     *
     * @TOOD: For when we "jump" namespaces, figure out how to precede mappings with their namespace
     *
     * @param string $name A pnuemonic, label or variable name
     * @return string Variable value or response from remote resource
     */
    public function __get($name) {
        $retval = null;
        if (!is_array($name)) {
             if (isset($this->_data[$name])) {
                $retval = $this->_data[$name];
            }
        }
        return $retval;
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
     * 
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
    
    protected function pullFromCache($namespace=false,$call_name=false,$arguments=[]) {
        ksort($arguments);
        array_map('mb_strtoupper',$arguments);
        $res = Humble::cache($namespace.'-'.$call_name.'-'.md5(http_build_query($arguments)));
        //Log::general($res ? 'Pulled From Cache' : 'Not Cached');
        return $res;
    }
    
    /**
     * If any call parameter value is marked as a secret, we attempt to retrieve that secret here and substitute the value
     * 
     * @param array $call
     * @return array
     */
    protected function processSecrets($call=[]) {
        $sm = Humble::getEntity('humble/secrets/manager');
        foreach ($call as $key => $val) {
            if (is_array($val)) {
                $call[$key] = $this->processSecrets($val);
            } else {
                if (strtolower(substr($val,0,5))==='sm://') {
                    if ($x = $sm->reset()->setSecretName(substr($val,5))->setNamespace($this->_namespace())->load(true)) {
                        $call[$key] = $sm->decrypt(true)->getSecretValue();
                    }
                }
            }
        }
        return $call;
    }
    
    /**
     * 
     * 
     * @param type $name
     * @return varied
     */
    protected function _remoteProcedureCall($name=false) {
        $retval = null;
        if ($name && $this->_RPC()) {
            if (!\Singleton::mappings()) {
                if (!$default_mappings = Humble::cache('yaml-humble')) {
                    Humble::cache('yaml-humble',$default_mappings = yaml_parse(file_get_contents('Code/Base/Humble/RPC/mapping.yaml')));
                }
                \Singleton::mappings($default_mappings); //default mappings
            }
            if (strtolower($this->_namespace()) !== 'humble') {
                if ($me = Humble::getModule($this->_namespace())) {
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
                $call    = $this->processSecrets(\Singleton::mappings()[$name]);
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
                        if (is_string($call['arguments']) && (substr($call['url'],strlen($call['url'])-1,1)=='+')) {
                            $method      = 'get'.ucfirst($call['arguments']);
                            $call['url'] = substr($call['url'],0,strlen($call['url'])-1).'/'.$this->$method();
                        } else {
                            $args        = array_merge($args,$this->_processArguments($call));
                        }
                        $userid = (isset($call['userid'])   && $call['userid'])     ? $call['userid']   : false;
                        $passwd = (isset($call['password']) && $call['password'])   ? $call['password'] : false;
                        $secure = (isset($call['secure'])   && $call['secure'])     ? $call['secure']   : false;
                        //$retval = $this->_curl($call['url'],$args,$call['method'],$secure,$userid,$passwd);
                        if (isset($call['cache'])) {
                            if ($retval = $this->pullFromCache($this->_namespace(),$name,$args)) {
                                return $retval;                                 //this ain't the way m8
                            }
                        }
                        $retval = $this->_hurl($call['url'],$args,$call,$secure,$userid,$passwd);  //going to use _hurl until curl starts working again
                        if (isset($call['cache'])) {
                            $this->pushToCache($this->_namespace(),$name,$args,$call['cache'],$retval);
                        }
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

    protected function _unset($name=false) {
        if (($name) && isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
        return $this;
    }

    /**
     * The setter magic method.
     *
     * Just stores the name/value pair passed in to the internal array.
     *
     * @param string $name Name of variable in the name/value pair
     *
     * @param string $value Value of variable in the name/value pair
     */
    public function __set($name,$value)   {
        $this->_data[$name] = $value;
        return $this;
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
                if (($retval = $this->_remoteProcedureCall($name)) === null) {
                    \Log::console("Undefined Method: ".$name." invoked from ".$this->getClassName().".");
                }
                return $retval;
            }
        }
    }

    /**
     * Checks to see if if a particular module is enabled
     *
     * @param array $module
     * @return boolean If module is enabled
     */
    public function isAvailable($module) {
        $ok = true;
        if (isset($this->_modules[$module['type']])) {
            $ok =  ($this->_modules[$module['type']]['enabled'] == 'Y');
        } else {
            $ref = Humble::getEntity('humble/modules');
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
        } else {
            return $this->_prefix;
        }
        return $this;
    }

    public function _messages($msg=null) {
        if ($msg!==null) {
            $this->_messages[] = $msg;
            return $this;
        } else {
            return $this->_messages;
        }
    }

    public function _errors($msg=null) {
        if ($msg!==null) {
            $this->_errors[] = $msg;
            return $this;
        } else {
            return $this->_errors;
        }return $this;
    }

    public function _warnings($msg=null) {
        if ($msg!==null) {
            $this->_warnings[] = $msg;
            return $this;
        } else {
            return $this->_warnings;
        }
    }

    public function _notices($msg=null) {
        if ($msg!==null) {
            $this->_notices[] = $msg;
            return $this;
        } else {
            return $this->_notices;
        }
    }

}