<?php
namespace Code\Base\Humble\Helpers;
use Log;
use Humble;
/**
 *
 * Just some helper functions for the admin page
 *
 * Directory functions
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Core_Helper_Directory.html
 * @since      File available since Version 1.0.1
 */
class Admin extends Helper
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
     * Just a helper function to return a gif indicating whether a directory exists or not, used only by the admin home page
     *
     * @param string $file
     * @param type $module
     * @return string
     */
    public function exists($file,$module) {
        $package = $module['package'];
        $icon = '<img style="cursor: pointer" onclick="Humble.admin.create(\''.$file.'\',\''.$package.'\')" height="15" src="/web/images/redx.gif" title="This directory is missing"  style="float: left; margin-right: 3px" />';
        if (strlen(trim($file)) != 0) {
            $file = 'Code/'.$package.'/'.str_replace('_','/',$file);
            if (file_exists($file)) {
                $icon = '<img src="/web/images/check.png" title="This directory is present"  style="float: left; margin-right: 3px" height="18" />';
            }
        } else {
            $icon = '<span style="color: #b00">MISSING XML ENTRY</span>';
        }
        return $icon;
    }

    /**
     * Because Twig has issues with magic methods, this is just a helper to do something that Smarty has no problem with...
     *
     * @param type $namespace
     */
    public function moduleInformation($namespace=false) {
        $data   = [];
        if ($namespace) {
            $data = Humble::getEntity('core/modules')->setNamespace($namespace)->load(true);
        }
        return $data;
    }

}