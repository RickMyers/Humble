<?php
namespace Base\Paradigm\Helpers;
use Humble;
/**
 * String utilities
 *
 * String related functions, particularly around token substitution
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@enicity.com
 * @since      File available since Release 1.0.0
 */
class Str extends Helper implements \Countable, \ArrayAccess, \IteratorAggregate
{

    private $string = null;

    /**
     * Constructor
     */
    public function __construct($string=false, $exceptions = null) {
        if ($string) {
            $table = Humble::translateTable();
            $this->string = (($table) ? str_replace($table['from'],$table['to'],$string) : $string);
        }
    }

    /**
     *
     *
     * @param array $strings
     * @return array
     */
	public static function createStrings(array $strings)	{
		foreach ($strings as &$string)		{
			$string = new static($string);
		}
		return $strings;
	}

    public function getIterator() {
        return new ArrayIterator(static::createStrings(preg_split('#(?<!^)(?!$)#u'), $this->string));
    }

	public function length() {
		return strlen($this->string);
	}

	public function part($start, $length = null)	{
		$this->string = substr($this->string, $start, $length);
		return $this;
	}

    public function offsetExists($offset=false) {
        return $this->length() >= $offset;
    }

    public function offsetSet($offset=false,$value) {
		if ($this->length() < $offset) return;
		$string = clone $this;
		$start = $string->part(0, $offset);
		$string = clone $this;
		$end = $string->part($offset+1);
		$this->string = $start.$value.$end;
		return $this;
    }

    public function offsetGet($offset=false) {
		$string = clone $this;
		return $string->part($offset, 1);
    }

    public function offsetUnset($offset=false) {
        $this->offsetSet($offset, '');
		return $this;
    }

	public function append($string)	{
		$this->string .= $string;
		return $this;
	}

	public function prepend($string) {
		$this->string = $string.$this->string;
		return $this;
	}

	public function lower()	{
		$this->string = strtolower($this->string);
		return $this;
	}

	public function upper()	{
		$this->string = strtoupper($this->string);
		return $this;
	}

    /**
     * Multi-purpose function to return either the array length or string length of a passed in value or the default private string
     *
     * @param mixed $what
     * @return int
     */
    public function count($what=false) {
        $total = 0;
        $what  = ($what) ? $what : ($this->string ? $this->string : null);
        if ($what) {
            $total = (is_array($what)) ? count($what) : strlen($what);
        }
        return $total;
    }

    public function __toString() {
        return $this->string;
    }

    /**
     * Formats a date to a standard output format
     *
     * @param type $arg
     * @return string
     */
    private function date($arg=false) {
        $text = '??';
        if ($arg) {
            $text = date('m/d/Y',strtotime($arg));
        }
        return $text;
    }

    /**
     * Formats a date to a standard output format
     *
     * @param type $arg
     * @return string
     */
    private function datetime($arg) {
        $text = '??';
        if ($arg) {
            $text = date('m/d/Y H:i:s',strtotime($arg));
        }
        return $text;
    }

    /**
     * A function has been detected in the activity message, parse it out and execute it
     *
     * @param type $text
     */
    private function expandFunctions($text) {
        $data = explode('@@',' '.$text);
        $text = '';
        for ($i = 0; $i < count($data); $i++) {
            if (($i%2) == 0) {
                $text .= $data[$i];
            } else {
                $func = explode('(',$data[$i]);
                $arg  = isset($func[1]) ? substr($func[1],0,strlen($func[1]-1)) : false;
                if ($func[0] && $arg) {
                    eval('$text .= $this->'.$func[0].'('.$arg.');');
                }
            }
        }
        return $text;
    }

    /**
     * Employs a substitution algorithm to replace parts of a string with segmented values
     *
     * @param type $text
     * @param type $options
     * @return type
     */
    public function translate($text=false,$options=[]) {
        $text = ($text) ? $text : ($this->string ? $this->string : '');
        if (strpos($text,'%%') !== false) {
            $data = explode('%%',' '.$text);
            $text = '';
            for ($i = 0; $i < count($data); $i++) {
                if (($i%2) == 0) {
                    $text .= $data[$i];
                } else {
                    $parts = explode('.',$data[$i]);
                    $var = '$options';
                    for ($j = 0; $j < count($parts); $j++) {
                        $var .= "['".$parts[$j]."']";
                    }
                    eval('$text .= '."$var".';');
                }
            }
        }
        if (strpos($text,'@@')!==false) {
            $text = $this->expandFunctions($text);
        }
        return trim($text);
    }
}