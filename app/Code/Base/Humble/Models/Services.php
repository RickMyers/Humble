<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Service Description
 *
 * A class to parse and manipulate the services that make up each
 * controller
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humblecoding.com>
 * @copyright  2005-present Humble
 * @license    https://humblecoding.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://humblecoding.com/docs/class-Services.html
 * @since      File available since Release 1.0.0
 */
class Services extends Model
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

    public function explain($package=false,$main_module=false) {
        $modules    = [];
        $packages   = ($package) ? [$package] : ($this->getPackage() ? [$this->getPackage()] : Humble::getPackages());
        foreach ($packages as $package) {
            $modules = ($main_module) ? [$main_module] : ($this->getModule() ? [$this->getModule()] : Humble::getModules($package)); 
            foreach ($modules as $module) {
                
            }
        }
    }
}