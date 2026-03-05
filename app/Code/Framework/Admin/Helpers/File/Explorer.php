<?php
namespace Code\Framework\Admin\Helpers\File;
use Humble;
use Log;
use Environment;
/**
 *
 * File Explorer Helper
 *
 * see description
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Other
 * @author     Rick rick@humbleprogramming.com
 */
class Explorer extends \Code\Framework\Admin\Helpers\Helper
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
    public function className():string {
        return __CLASS__;
    }

    /**
     * Returns a list of files in JSON format that reside at a particular path
     * 
     * @return type
     */
    public function files($path=false):string {
        $files = []; $first=true;
        if ($path = ($path) ? $path : $this->getPath()) {
            if (chdir($path)) {
                foreach (explode("\n",`ls -l | awk '{print $1","$2","$3","$4","$5","$6","$7","$8","$9}'`) as $line) {
                    $parts   = explode(',',$line);
                    if (!isset($parts[7])) {
                        continue;
                    }
                    if ($first) {
                        $first = false;
                        continue;
                    }
                    $files[] = [
                       "directory" => (substr($parts[0],0,1)==='d'),
                       "permissions" => substr($parts[0],1),
                       'links' => $parts[1],
                       'owner'     => $parts[2],
                       'group'     => $parts[3],
                       'filesize'  => $parts[4],
                       'modified'  => $parts[5].' '.$parts[6].' '.$parts[7],
                       'name'      => $parts[8]
                    ];
                }
            }
        }
        return json_encode($files,JSON_PRETTY_PRINT);
    }
    
    /**
     * Deletes a file
     * 
     * @return bool
     */
    public function delete():bool {
        $deleted = false;
        if (($dir = $this->getDirectory()) && ($file = $this->getFile())) {
            if (file_exists($dir.'/'.$file)) {
                $deleted = unlink($dir.'/'.$file);
            }
        }
        return $deleted;
    }

    /**
     * Sets up for an Ace editor editing session
     * 
     * @return string
     */
    public function edit():string {
        if (($dir = $this->getDirectory()) && ($file = $this->getFile())) {
            $parts = explode('.',$file);
            $extension = (count($parts)!==1) ? $parts[count($parts)-1] : 'text';
            $this->setFileExtension($extension);
            return (file_exists($dir.'/'.$file)) ? file_get_contents($dir.'/'.$file) : 'File not found';
        }
        return '';
    }
    
    /**
     * Attempts to save off a file, possibly using the elevated command proxy to do so
     * 
     * @return bool
     */
    public function save($filename=false,$source=''):bool {
        $result   = false;
        $f = $this->getFilename();
        $filename = ($filename) ? $filename : ($this->getFilename() ? $this->getFilename() : false);
        $source   = ($source)   ? $source   : ($this->getSource()   ? $this->getSource() : false);
        $elevated = $this->getElevated() ? $this->getElevated() : false;
        if ($filename && $source) {
            if (file_exists($filename)) {
                if ($elevated === 'Y') {
                    $result = \Environment::saveFile($filename,$source);
                } else {
                    $result = file_put_contents($filename,$source);
                }
            }
        }
        return $result;
    }
    
}