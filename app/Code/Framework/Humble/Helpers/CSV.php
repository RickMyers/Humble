<?php
namespace Code\Framework\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**
 * 
 * CSV Utilities
 *
 * See Title
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Other
 * @author     Richard Myers rick@humbleprogramming.com
  * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
class CSV extends Helper
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
     * 
     * 
     * @param type $filename
     * @return array
     */
    public function toArray($filename = false) {
        $arr = [];
        if ($filename && file_exists($filename)) {
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($arr[] = fgetcsv($handle, 0, ",")) !== FALSE) {  }
                fclose($handle);
            }
        }
        return $arr;
    }   

    /**
     * Process a CSV into a hash table with the column names as the index instead of 
     * 
     * @param string $str
     * @return array
     */
    public function strToHashTable($str=false,$keepCase=false,$separator=',') {
        $csv = null;
        if ($str) {
           $str      = is_array($str) ? implode("\n",$str) : $str;
           $tempfile = tempnam("/tmp","csv");
           file_put_contents($tempfile,$str);
           $csv      = $this->toHashTable($tempfile,$keepCase,$separator);
           unlink($tempfile);
        }
        return $csv;
    }
    
    public function arrayToHash($data,$keepCase=true) {
        $arr = []; $first = true;
        foreach ($data as $line) {
            if ($first) {
                $fields = $line; 
                $first = false;
                continue;
            }           
            $row = [];
            foreach ($line as $idx => $value) {
                if ($keepCase) {
                    $row[(isset($fields[$idx]) ? $fields[$idx] : 'field_'.$idx)] = $value;
                } else {
                    $row[strtoupper((isset($fields[$idx]) ? $fields[$idx] : 'field_'.$idx))] = $value;
                }
            }
            $arr[] = $row;        
        }
        return $arr;
    }

    
    /**
     * Different version of loading a CSV
     * 
     * @param type $filename
     * @param type $stripchars
     * @param type $separator
     * @return type
     */
    public function loadCSV($filename=false,$stripchars=false,$separator=',') {
        $arr = [];
        if ($filename && file_exists($filename)) {
            $fields = [];
            $first  = true;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
                    if ($first) {
                        if ($stripchars) {
                            foreach ($data as $key => $val) {
                                $val    = str_replace(' ','_',$val);
                                $data[] = preg_replace("/[^A-Za-z0-9_]/", '', $val);  //strip non alphanumeric chars
                            }
                        }
                        $fields = $data; 
                        $first  = false;
                        continue;
                    }
                    $row = [];
                    foreach ($data as $idx => $value) {
                        $row[strtolower(isset($fields[$idx]) ? $fields[$idx] : 'field_'.$idx)] = $value;
                    }
                    $arr[] = $row;
                }
                fclose($handle);
            }
            $this->setFields($fields);
        }
        return $arr;
        
    }
    /**
     * 
     * @param type $filename
     * @return array
     */
    public function toHashTable($filename=false,$keepCase=false,$separator=',') {
        $arr = [];
        if ($filename && file_exists($filename)) {
            $fields = [];
            $first  = true;
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
                    if ($first) {
                        foreach ($data as $key => $val) {
                            $data[$key] = preg_replace("/[^A-Za-z0-9 ]/", '', $val);  //strip non alphanumeric chars
                        }
                        $fields = $data; 
                        $first = false;
                        continue;
                    }
                    $row = [];
                    foreach ($data as $idx => $value) {
                        if ($keepCase) {
                            $row[(isset($fields[$idx]) ? $fields[$idx] : 'field_'.$idx)] = $value;
                        } else {
                            $row[strtoupper((isset($fields[$idx]) ? $fields[$idx] : 'field_'.$idx))] = $value;
                        }
                    }
                    $arr[] = $row;
                }
                fclose($handle);
            }
        }
        return $arr;
    }
    
    /**
     * Reverses a hash table to a CSV
     * 
     * @param type $data
     * @param type $delimiter
     * @param type $enclosure
     * @return string
     */
    public function toCSV($data, $delimiter = ',', $enclosure = '"',$value_headers=false) {
        $handle = fopen('php://temp', 'r+');
        $headers=[]; $contents = '';
        foreach ($data as $idx => $line) {
            if (!$headers) {
                foreach ($line as $field => $value) {   
                    $headers[] = trim(preg_replace("/[^A-Za-z0-9 _]/", '', ($value_headers ? $value : $field)));
                }
                if (!$value_headers) {
                    fputcsv($handle, $headers, $delimiter, $enclosure);
                }
            }            
            fputcsv($handle, $line, $delimiter, $enclosure);
        }
        rewind($handle);
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
    }    
    
    /**
     * Writes an array out to a file as a CSV
     * 
     * @param type $list
     * @param type $filename
     * @return type
     */
    public function write($list=[],$filename=false) {
        $fp = fopen($filename, 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        return fclose($fp);        
    }
    
    /**
     * Converts a hash table back to the original array
     * 
     * @param type $data
     * @return array
     */
    public function hashToArray($data=[]) {
        $headers = [];
        $results = [];
        foreach ($data as $row) {
            $new_row = [];
            if (!$headers) {
                foreach ($row as $column=>$val) {
                    $headers[] = $column;
                }
                $results[] = $headers;
            }
            foreach ($row as $val) {
                $new_row[] = $val;
            }                
            $results[] = $new_row;
        }
        return $results;
    }
    
    /**
     * Converts an array (possibly two dimensional) into csv
     * 
     * @param type $arr
     * @param type $separator
     * @param type $headers
     * @return boolean
     */
    public function ARRAY2CSV($arr = false,$separator=',',$headers = false) {
        if ($arr) {
            if ($headers) {
                header("Content-Type: text/csv");
                header('Content-Disposition: inline; filename="output.csv"');
            }
        }
        foreach ($arr as $idx => $row) {
            if (is_array($row)) {
                print('"'.implode('","',$row).'"'."\n");
            } else {
                print(implode("\t",explode($separator,$row))."\n");
            }
        }
        return true;
    }
    
    /**
     * Converts an array (possibly two dimensional) into csv
     * 
     * @param type $arr
     * @param type $separator
     * @param type $headers
     * @return boolean
     */
    public function arrayToCSV($arr = false,$separator=',') {
        $ret = '';
        foreach ($arr as $idx => $row) {
            if (is_array($row)) {
                $ret .= ('"'.implode('","',$row).'"'."\n");
            } else {
                $ret .= (implode("\t",explode($separator,$row))."\n");
            }
        }
        return $ret;
    }    
}