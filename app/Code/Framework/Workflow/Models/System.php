<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * System Events
 *
 * Time based system event methods (think CRON)
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @since      File available since Version 1.0.1
 */
class System extends Model
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
    private function registerSystemTrigger($id,$data) {
        $integration_point = Humble::entity('paradigm/webservice_workflows');
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
        $data           = json_decode($this->getData(),true);
        $this->setWindowId($data['window_id']);  //now I need a shower...
        $component      = Humble::model('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        
        
        /**$webservice     = Humble::entity('paradigm/webservices');
        $webservice->setUri($data['uri']);
        $webservice->setWebserviceId($data['id']);
        $webservice->setActive($data['enabled']);
        $id = $webservice->save();
        $this->registerWebServiceIntegrationPoint($id,$data);*/
    }
}
