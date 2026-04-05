<?php
namespace Code\Framework\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * Client template helper methods
 *
 * see above...
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Framework
 * @author     Myers rick@humbleprogramming.com
 */
class Template extends Helper
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
    public function className() {
        return __CLASS__;
    }

    /**
     * Attempts to retrieve a template from the app web directory
     * 
     * @return type
     */
    public function fetchTemplate() {
        $template   = 'Template not found';
        $namespace  = $this->getNamespace();
        $identifier = $this->getIdentifier();
        if ($namespace && $identifier) {
            $module = \Humble::module($namespace);
            $path = 'Code/'.$module['package'].'/'.$module['module'].'/web/app/'.$identifier.'.tpl';
            if (file_exists($path)) {
                $template = file_get_contents($path);
            }
        }
        return $template;
    }
}