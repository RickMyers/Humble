<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * File Trigger Methods
 *
 * See Title
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2005-present Humble
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-File.html
 * @since      File available since Release 1.0.0
 */
class File extends Model
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

    public function registerFileTrigger($id,$data) {
        
    }
    /**
     * We use the normal component save here, and then register the integration point
     *
     */
    public function save() {
        $data         = json_decode($this->getData(),true);
        $this->setWindowId($data['windowId']);  //now I need a shower...
        $component    = Humble::getModel('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        $trigger     = Humble::getEntity('paradigm/file/triggers');
        $trigger->setFileTriggerId($data['id']);
        $trigger->setActive($data['active']);
        $id = $trigger->save();
        $this->registerFileTrigger($id,$data);
    }
}