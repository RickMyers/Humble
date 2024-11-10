<?php
namespace Code\Framework\Workflow\Models;
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

    /**
     * We use the normal component save here, and then register the integration point
     *
     */
    public function save() {
        $data         = json_decode($this->getData(),true);
        $this->setWindowId($data['window_id']);  //now I need a shower...
        $component    = Humble::model('workflow/manager')->setData($this->getData())->saveComponent();
        return Humble::entity('paradigm/file/triggers')->setFileTriggerId($data['id'])->setWorkflowId($data['workflow_id'])->setDirectory($data['directory'])->setExtension($data['file_mask'])->setField($data['field'])->setActive($data['active'])->save();
    }
}