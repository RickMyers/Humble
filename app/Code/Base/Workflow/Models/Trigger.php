<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Event Trigger Methods
 *
 * Some methods for special handling of triggered events
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <>
 * @copyright  2005-present Humble
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Trigger.html
 * @since      File available since Release 1.0.0
 */
class Trigger extends Model
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

    public function execute() {
        $el = $this->getElement();
        $event_listener = Humble::getEntity('paradigm/event/listeners');

    }
}