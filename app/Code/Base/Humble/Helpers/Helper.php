<?php
namespace Code\Base\Humble\Helpers;
/**
 * The base class from which all other helpers derive
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Humble
 * @author     Original Author <rick@enicity.com>
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://license.enicity.com
 * @version    1.0.1
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Version 1.0.1
 */
class Helper extends \Code\Base\Humble\Models\Model {
    private $xml            = null;
    protected $errors       = null;
    protected $isVirtual    = false;

    public function __construct()    {
        parent::__construct();
    }

    /**
     *
     * @return system
     */
    public function getClassName()    {
        return __CLASS__;
    }

    /**
     *
     * @param type $xml
     * @return type
     *
     * @TODO:  Refactor all of the XML routines out of here and into their own XML helper!!!!!!!!
     */
    public function isValidXML($xml=false)    {
		$this->xml = ($xml!==false) ? $xml : $this->getXML();
        if ($this->xml !== null) {
            libxml_use_internal_errors(true);
            @$doc = new \DOMDocument('1.0', 'utf-8');
            @$doc->loadXML($this->xml);
            $this->errors = libxml_get_errors();
        }
		return ($this->xml === null) ? false : (count($this->errors) == 0);
    }

    /**
     *
     * @param type $xml
     * @return \SimpleXmlElement
     */
    public function stringToXML($xml) {

        $xmlObj = null;
        if ($this->isValidXML($xml)) {
            $xmlObj = new SimpleXmlElement($xml);
        }
        return $xmlObj;
    }

    /**
     *
     * @param type $arg
     */
    public function toConsole($arg) {
        \Log::console($arg);
    }

    /**
     *
     * @param type $xml
     * @return type
     */
	public function toArray($xml=null)
	{
		$xml = ($xml) ? $xml : $this->getXML();
		return json_decode(json_encode((array) simplexml_load_string($xml)),1);
	}

    /**
     *
     * @param type $xml
     * @return type
     */
	public function toJSON($xml=null)
	{
		$xml = ($xml) ? $xml : $this->getXML();
		return json_encode((array) simplexml_load_string($xml));
	}

    /**
     * This is a helper for templaters, takes a string and converts to a JSON array
     *
     * @param type $json
     * @return boolean
     */
    public function JSON($json=false) {
        if ($json) {
            return json_decode($json,true);
        }
        return false;
    }
    /**
     *
     * @param type $arr
     * @param type $node
     * @return string
     */
    private function traverseXML($arr,$node)    {
        $xml = "<".$node.">";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= (string)$this->traverseXML($val,$key);
            } else {
                if ($key) {
                    $xml .= "<".$key.">".$val."</".$key.">";
                } else {
                    $xml .= $val;
                }
            }
        }
        $xml .= "</".$node.">";
        return $xml;
    }

    /**
     *
     * @param type $arr
     * @param type $root
     * @return string
     */
    public function toXML($arr=[],$root="root")    {
        $xml = "<?xml version=\"1.0\" standalone=\"yes\"?><".$root.">";  //<? for the sake of code parsing
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $xml .= (string)$this->traverseXML($val,$key);
                } else {
                    $xml .= "<".$key.">".$val."</".$key.">";
                }
            }
        }
        $xml .= "</".$root.">";
        return $xml;
    }

    /**
     *
     * @param type $xml
     * @return type
     */
    public function toSimpleXML($xml=null)    {
    	$xml = ($xml) ? $xml : $this->getXML();
		return simplexml_load_string($xml);
    }

    /**
     *
     * @param type $whichOne
     * @param type $whereTo
     * @return type
     */
    public function moveFile($whichOne=false,$whereTo = false) {
        $moved = false;
        if ($whichOne) {
            $fileData = 'get'.ucfirst($whichOne);
            $fileData = $this->$fileData();
            if ($fileData) {
                if (isset($fileData['tmp_name']) && isset($fileData['name'])) {
                    $moved = copy($fileData['tmp_name'],$whereTo.'/'.$fileData['name']);
                }
            }
        }
        return $moved;
    }

    /**
     *
     * @param type $file
     * @return type
     */
    public function deleteFile($file) {
        $didit = false;
        if (file_exists($file)) {
            $didit = unlink($file);
        }
        return $didit;
    }

    /**
     *
     * @param type $url
     * @return type
     */
    public function curlFetch($url) {
        $ch             = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 61);

        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9");
        $res = (string)curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     *
     * @param type $url
     * @return type
     */
    public function fetch($url=false) {
		ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
		$opts = array(
			'http'=>array(
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n" .
			"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9\r\n"
			)
		);
        $retval = '';

        if ($url) {
            \Log::console('Attempting to fetch '.$url);
            $context = stream_context_create($opts);
            $handler	= fopen($url,"r",false,$context);
            if ($handler) {
                while (!feof($handler))
                    $retval	.= fread($handler,4096);
                fclose($handler);
            } else {
                \Log::console('Unable to connect to fetch resource');
            }
        }
        return $retval;
    }

    /**
     * non-memory intensive way of retrieving a file
     *
     * @param type $src
     * @param type $dest
     * @return type
     */
    public function fetchFile($src,$dest) {
        $size   = 0;
        $out    = fopen($dest,"w");
        $read   = fopen($src,"r");
        if ($out && $read) {
            while (!feof($read)){
                $size += fwrite($out,fread($read,8192));
            }
        }
        fclose($out);
        fclose($read);
        return $size;
    }

   /**
     * Returns just the extension portion of a file without the filename
     *
     * @param type $name
     * @return type
     */
    public function getExtension($name)    {
        $extension = '';
        $name = $this->getFileName($name);
        if (strpos($name,'.') !== -1) {
            $extension = substr($name,strrpos($name,'.')+1);
        }
        return strtolower($extension);
    }

    /**
     * Returns just the filename portion of a file without the extension
     *
     * @param type $name
     * @return type
     */
    public function getFileName($name)    {
        $filename = '';
        if (strpos($name,'/') !== false) {
            $filename = substr($name,strrpos($name,'/')+1);
            $filename = (strpos($filename,"?") ? substr($filename,0,strpos($filename,"?")) : $filename);
            $filename = (strpos($filename,"&") ? substr($filename,0,strpos($filename,"&")) : $filename);
        } else {
            $filename = $name;
        }
        return strtolower($filename);
    }

    /**
     * returns minus the extension
     *
     * @param type $name
     * @return type
     */
    public function getBaseFileName($name)    {
        $filename = '';
        if (strpos($name,'/') !== false) {
            $filename = substr($name,strrpos($name,'/'));
        } else {
            $filename = $name;
        }
        if (strpos($filename,'.') !== false) {
            $filename = substr($filename,0,strrpos($filename,'.'));
        }
        return $filename;
    }

    /**
     * Youtube special handling
     *
     * @param type $url
     * @return type
     */
    public function scrubURL($url) {
        $srch = array('youtube.com/embed/','youtu.be/','youtube.com/watch?v=');
        $repl = array('youtube.com/v/','youtube.com/v/','youtube.com/v/');
        $newURL = str_replace($srch, $repl, $url);;
        return $newURL;
    }

    /**
     *
     * @param type $data
     * @return type
     */
    public function processEmbedData($data=false) {
        if (!$data) {
            $data = ($this->getJson()) ? $this->getJson() : (($this->getData()) ? $this->getData : null);
        }
        $data = json_decode($data);
        return $data->attributes[0];
    }

    /**
     * Will return all files in that directory, including subdirectories
     *
     * @param type $dir
     * @return array
     */
    public function filesInDirectory($dir=false) {
        $files = [];
        if (($dir) && (is_dir($dir))) {
            $handler = dir($dir);
            while (($entry = $handler->read()) !== false) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir($dir.'/'.$entry)) {
                    $files = array_merge($files, $this->filesInDirectory($dir.'/'.$entry));
                } else {
                    $files[] =  $dir.'/'.$entry;
                }
            }
        }
        return $files;
    }

    /**
     *
     */
    public function _isVirtual($state=null) {
        if ($state === null) {
            return $this->_isVirtual;
        } else {
            $this->_isVirtual = $state;
        }
        return $this;
    }

    //--------------------------------------------------------------------------------------------------
    // Getters/Setters
    //--------------------------------------------------------------------------------------------------
    public function getErrors()         {   return $this->errors;               }
    public function getXML()            {   return $this->xml;                  }
    public function setXML($arg)        {   $this->xml                  = $arg; }

}
?>