<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Code presentation and editing methods
 *
 * See title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rickmyers1969@gmail.com>
 */
class Code extends Model
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

    public function fetchSourceCode() {
        $module     = Humble::module($this->getNamespace());
        $resource   = html_entity_decode($this->getResource());
        $parts      = explode('<br />',$resource);
        switch ($this->getType()) {
            case 'Model' :
                $source = 'Code/'.$module['package'].'/'.$module['models'].'/'.ucfirst($parts[1]).'.php';
                break;
            case 'Entity' :
                $source = 'Code/'.$module['package'].'/'.$module['entities'].'/'.ucfirst($parts[1]).'.php';
                break;
            case 'Helper' :
                $source = 'Code/'.$module['package'].'/'.$module['helpers'].'/'.ucfirst($parts[1]).'.php';
                break;
            case 'Collection' : 
                break;
            default :
                break;
        }
        return file_exists($source) ? file_get_contents($source) : "\n\nNo Source (likely virtual class)\n\n";
    }
}