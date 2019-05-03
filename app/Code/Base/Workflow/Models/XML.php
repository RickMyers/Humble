<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * XML related methods
 *
 * Utilities for managing XML fields in the event
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@enicity.com>
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class XML extends Model
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
     * Will convert an XML document into an array and attach it to the event in a field specified by the user
     *
     * @workflow use(process) configuration(/workflow/xml/translate)
     * @param type $EVENT
     */
    public function translate($EVENT=false) {
        if ($EVENT) {
            $data = $EVENT->load();
            $cnfg = $EVENT->fetch();
            if (isset($cnfg['source']) && isset($data[$cnfg['source']])) {
                if (isset($cnfg['field'])) {
                    $EVENT->update([$cnfg['field']=> unserialize(serialize(json_decode(json_encode((array) simplexml_load_string($data[$cnfg['source']])), 1)))]);
                }
            }
        }
    }
}