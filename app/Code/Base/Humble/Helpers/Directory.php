<?php
namespace Code\Base\Humble\Helpers;
/**
 *
 * Manages directory functions
 *
 * Directory functions
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@humblecoding.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0
 * @link       https://enicity.com/docs/class-.then(_Helper_Directory.html
 * @since      File available since Version 1.0.1
 */
class Directory extends File
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
     * When passed a directory either by argument or parameter, attempts to create it
     *
     * @param type $dir
     */
    public function create($dir=false) {
        $result = false;
        if ($directory = ($dir) ? $dir : ($this->getDirectory() ? $this->getDirectory() : false)) {
            $result = @mkdir($directory,0775,true);
        }
        return $result;
    }

    /**
     * Copies a directory
     *
     * @param type $source
     * @param type $destination
     * @param type $skipIfPresent
     */
    public function copyDirectory($source,$destination,$skipIfPresent=false)    {
        $handler = dir($source);
        while (($entry = $handler->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (is_dir($source.'/'.$entry)) {
                @mkdir($destination.'/'.$entry);
                $this->copyDirectory($source.'/'.$entry,$destination.'/'.$entry,$skipIfPresent);
            } else {
                $s      = $source.'/'.$entry;
                $d      = $destination.'/'.$entry;
                if ($skipIfPresent && file_exists($d)) {
                  //  print('Notice: skipping '.$d." since it is present in destination\n");
                } else {
                //    print('copying: '.$s.' to '.$d."\n");
                    copy($s,$d);
                }
            }
        }
    }

    /**
     * Fetches the full contents of a directory
     *
     * @param type $path
     * @param type $includePath
     * @param type $mask
     * @return type
     */
    public function contents($path,$includePath=false,$mask=false)    {
        $entries = [];
        if ($path) {
            $dir = dir($path);
            while (($entry = $dir->read())!==false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir($path.'/'.$entry)) {
                    $entries = array_merge($entries,$this->contents($path.'/'.$entry,$includePath,$mask));
                } else if ($mask && (strpos($entry,$mask) !== false)) {
                    $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                } else if (!$mask) {
                    $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                }
            }
        }
        return $entries;
    }

    /**
     * Lists the contents of a directory
     *
     * @param type $path
     * @param type $includePath
     * @param type $mask
     * @return type
     */
    public function listDirectory($path,$includePath=false,$mask=false)    {
        $entries = [];
        if ($path) {
            $dir = dir($path);
            while (($entry = $dir->read())!==false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if ($mask && (strpos($entry,$mask) !== false)) {
                    $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                } else if (!$mask) {
                    $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                }
            }
        }
        return $entries;
    }

    /**
     * Lists the subdirectories of a directory
     *
     * @param type $path
     * @param type $includePath
     * @param type $mask
     * @return type
     */
    public function listSubdirectories($path,$includePath=false,$mask=false)    {
        $entries = [];
        if ($path) {
            $dir = dir($path);
            while (($entry = $dir->read())!==false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir($path.'/'.$entry)) {
                    if ($mask && (strpos($entry,$mask) !== false)) {
                        $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                    } else if (!$mask) {
                        $entries[] = ($includePath) ? $path.'/'.$entry : $entry;
                    }
                }
            }
        }
        return $entries;
    }
    /**
     * Copies one directory into another
     *
     * @param type $source
     * @param type $destination
     */
    public function updateDirectory($source,$destination)    {
        $handler = dir($source);
        while (($entry = $handler->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (is_dir($source.'/'.$entry)) {
                @mkdir($destination.'/'.$entry);
                $this->copyDirectory($source.'/'.$entry,$destination.'/'.$entry);
            } else {
                $here = getcwd();
                $s = $source.'/'.$entry;
                $d = $destination.'/'.$entry;
                if (!file_exists($d)) {
                    \Log::console('copying: '.$s.' to '.$d."\n");
                    copy($s,$d);
                } else {
                    \Log::console('Skipping copy of '.$d);
                }
            }
        }
    }

    /**
     * Removes all files from a directory, optionally recursing subdirectories as well and purging them
     *
     * @param string $dir
     * @param boolean $recurse
     * @return type
     */
    public function purgeDirectory($dir,$recurse=false,$mask=false) {
        $removed = [];
        if (is_dir($dir) && !is_link($dir)) {
            $handler = dir($dir);
            while ($entry = $handler->read()) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                $target = $dir.'/'.$entry;
                if (!is_dir($target) && !is_link($target) && file_exists($target)) {
                    //@TODO: if mask, check if it matches mask before deleting
                    $removed[] = $target;
                    unlink($target);
                } else if (is_dir($target) && !is_link($target) && $recurse) {
                    $removed = array_merge($removed,$this->purgeDirectory($target,$recurse));
                }
            }
            $handler->close();
        }
        return $removed;
    }
}