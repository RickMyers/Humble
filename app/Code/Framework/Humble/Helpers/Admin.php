<?php
namespace Code\Framework\Humble\Helpers;
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
 * @author     Rick Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Helper_Directory.html
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
        $icon = '<img onclick="Administration.create.directory(\''.$file.'\',\''.$package.'\')" src="/web/images/redx.gif" title="This directory is missing"  style="cursor: pointer; float: left; margin-right: 3px" />';
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
            $data = Humble::entity('humble/modules')->setNamespace($namespace)->load(true);
        }
        return $data;
    }

    /**
     * This is a short cut based on the URI /home... it will look up the apps home page and redirect there, making sure we don't cause a cyclical infinite loop if the URI points to the redirect URI
     */
    public function appHomePage() {
        if ($project_data = \Environment::getProject()) {
            if ($project_data->landing_page && !(($project_data->landing_page == "\/humble\/home\/page") || ($project_data->landing_page == "/humble/home/page"))) {
                header("Location: ".$project_data->landing_page);
            }
        }
    }

}