<?php
namespace Code\Base\Workflow\Models;
use Humble;
/**    
 *
 * Webservice integration point manager
 *
 * Methods around establishing and executing integration with clients and
 * business partners
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Webhook extends Model
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
     * Now we correlate the integration point to the workflow
     *
     */
    private function registerWebhookIntegrationPoint($id,$data) {
        $integration_point = Humble::entity('paradigm/webhook/workflows');
        $integration_point->setWebserviceId($id);
        $integration_point->setWorkflowId($data['workflow_id']);
        $integration_point->setUri($data['uri']);
        $integration_point->save();
    }

    /**
     * We use the normal component save here, and then register the integration point
     *
     */
    public function save() {
        $data         = json_decode($this->getData(),true);
        $this->setWindowId($data['windowId']);  //now I need a shower...
        $component    = Humble::model('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        $webhook     = Humble::entity('paradigm/webhooks');
        $id = $webhook->setWebhook($data['webhook'])->setFormat($data['format'])->setDescription($data['description'])->setField($data['field'])->setActive($data['active'])->save();
        $this->registerWebhookIntegrationPoint($id,$data);
    }
}
