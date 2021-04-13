<?php
namespace Code\Base\Humble\Models;
use Humble;
/**
 * 
 * Form Generator
 *
 * Methods and other things associated with automatic form generation
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Forms extends Model
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
     * Returns an array of fields within a form
     *
     * @param type $formName
     */
    public function fields($formName=false,$fieldset=false) {
        $results = [];
        if ($formName) {
            $fields = Humble::getEntity('humble/form_fields');
            $fields->setFormName($formName);
            if ($fieldset) {
                $fields->setFieldset($fieldset);
            }
            $results = $fields->getFieldNames();
        }
        return $results;
    }
}