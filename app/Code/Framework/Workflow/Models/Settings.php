<?php
namespace Code\Framework\Workflow\Models;
/**    
 *
 * Configuration Settings Module
 *
 * See above
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Config_Models_Settings.html
 * @since      File available since Version 1.0.1
 */
class Settings extends Model
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
     * We are going to update a value in the settings section of the component
     */
    public function save() {
        $obj1 = \Humble::collection('paradigm/elements');
        $obj1->setId($this->getId());
        $data = $obj1->load();
        $obj2 = \Humble::collection('paradigm/elements');
        foreach ($data as $var => $val) {
            if ($var != '_id') {
                $method = 'set'.ucfirst($var);
                $obj2->$method($val);
            }
        }
        $settings = $data['settings'];
        if (!isset($settings->count)) {
            $settings->count = 1;
        } else {
            $settings->count = $settings->count + 1;
        }
        $settings->lastmodified = date('Y-m-d H:i:s');
        $obj2->setSettings($settings);
        $obj2->setId($this->getId());
        $obj2->save();
    }

}