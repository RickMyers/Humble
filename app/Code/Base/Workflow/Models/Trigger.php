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
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Trigger.html
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
    
    /**
     * We need to deviate from normal Mongo Paradigm element saving.  We are going to save the Mongo element and also register the listener for the workflow event
     */
    public function execute() {
        $el         = $this->getElement();
        $data       = json_decode($this->getData(),true);
        $d          = $el->setId($data['id'])->load();
        $enabled    = (isset($data['enabled']) && ($data['enabled']==='Y')) ? 'Y' : 'N';
        $el->setEnabled($enabled)->save();
        $listen_id  = \Humble::getEntity('paradigm/event/listeners')->setNamespace($d['namespace'])->setEvent($d['method'])->setWorkflowId($data['workflow_id'])->setActive($enabled)->save();
    }
}