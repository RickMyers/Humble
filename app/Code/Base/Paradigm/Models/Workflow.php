<?php
namespace Code\Base\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * Workflow Specific Functions
 *
 * see title
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Code.Base.Paradigm.Models.Workflow.html
 * @since      File available since Version 1.0.1
 */
class Workflow extends Model
{

    private $exporter = false;
    use \Code\Base\Humble\Event\Handler;

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
     * Creates a serialized structure of a workflow for sending to another server
     *
     * @return string
     */
    public function export() {
        if (!$this->exporter) {
            $this->exporter              = Environment::getProject();
            $this->_namespace($this->exporter->namespace);
        }
        $target = false; $file = false;
        if ($file = $this->getFile()) {

        }
            $target                      = Humble::getEntity('paradigm/export/targets')->setId($this->getDestinationId())->load();
        
        $results                         = [];
        $results[]                       = '#########################################################';
        $results[]                       = 'Beginning Export for '.$this->_namespace();

        $workflow                        = ["data"  => false,"webservice_workflow" => false,"webservice" => false,"listeners"=>false,"components" => [], "token"=>$target['token']];
        $workflow['data']                = Humble::getEntity('paradigm/workflows')->setId($this->getId())->load();
        unset($workflow['data']['image']);  //image sending can cause problems due to size
        $workflow['webservice_workflow'] = Humble::getEntity('paradigm/webservice_workflows')->setWorkflowId($workflow['data']['workflow_id'])->load(true);
        $workflow['listeners']           = Humble::getEntity('paradigm/workflow_listeners')->setWorkflowId($workflow['data']['workflow_id'])->load(true);
        if (isset($workflow['webservice_workflow']['webservice_id'])) {
            $workflow['webservice']      = Humble::getEntity('paradigm/webservices')->setId($workflow['webservice_workflow']['webservice_id'])->load();
        }

//        $destination                     = Humble::getEntity('paradigm/import_sources')->setId($this->getDestinationId())->load();
        $element                         = Humble::getCollection('paradigm/elements');
        $results[]                       = 'Exporting To '.$target['target'];
        if ($workflow['data']) {
            $results[]  = "Sending Components for Workflow [".$workflow['data']['title']."]";
            $component_list = json_decode($workflow['data']['workflow'],true);
            foreach ($component_list as $idx => $component) {
                $element->reset();
                $element->setId($component['id']);
                $results[]  = 'Exporting Component (MongoDB): '.$component['id'];
                $workflow['components'][$component['id']] = $element->load();
            }
  //          $whereTo = $destination['name'];
            if ($target) {
                Log::warning('Export Target: '.$target['target']);
                $this->setSessionId(true);
                $call = [
                    "url" => $target['target'].'/paradigm/workflow/import',
                    "api-key" => '',
                    "api-var" => '',
                    "secure"  => true,
                    "method"  => "POST",
                    "arguments" => [
                        "workflow" => ''
                    ]
                ];            

                $this->setWorkflow(json_encode($workflow));
                
                //$results[] = $this->_hurl($target['target'].'/paradigm/workflow/import',$this->_processArguments($call),$call);
            } else {
                header('Content-type: application/json');
                header('Content-Disposition: attachment; filename="'.$file.'"');
                return json_encode($workflow,JSON_PRETTY_PRINT);
            }
            //$results[] = $this->$whereTo();
        } else {
            $results[] = 'Missing a workflow '.$this->getId();
        }
        $results[] = 'Finished Sending'."\n------------------------------------------------------------\n";
        $results[] = '#########################################################';
        Log::warning($results);
        return $results;
    }

    /**
     * Will go through all the workflows and perform an export each one to a target server
     */
    public function sync() {
        foreach (Humble::getEntity('paradigm/workflows')->fetch() as $workflow) {
            $this->setId($workflow['id']);
            Log::warning('Exporting '.$workflow['workflow_id'].' @'.date('m/d/Y H:i:s'));
            $this->export();
        }
    }

    /**
     * Converts a serialized structure of a workflow to the original workflow
     */
    public function import() {
        //cache a copy of the workflow first for posterity sake
        $results  = [];
        $dest     = Environment::getRoot('paradigm').'lib/workflows/cache/';
        @mkdir($dest,0775,true);
        $results[] = '#########################################################';
        $results[] = 'Received a workflow'."\n\n";

        file_put_contents($dest.'workflow_'.time().'.dat',$this->getWorkflow());
        $workflow = json_decode($this->getWorkflow(),true);
        //&& count(Humble::getEntity('paradigm/import/tokens')->setToken($workflow['token'])->load(true))  do this differently
        if ($workflow) {
            $mysql                  = Humble::getEntity('paradigm/workflows');
            $webservice_workflow    = Humble::getEntity('paradigm/webservice_workflows');
            $webservice             = Humble::getEntity('paradigm/webservices');
            $listeners              = Humble::getEntity('paradigm/workflow_listeners');
            $element                = Humble::getCollection('paradigm/elements');
            $results[] = 'Removing Workflow (MySQL): '.$workflow['data']['id'];
            $mysql->setId($workflow['data']['id'])->delete();
            foreach ($workflow['data'] as $key => $value) {
                $method         = 'set'.ucfirst($key);
                $mysql->$method($value);
            }
            $wid = $mysql->save();
            $results[] = 'Added Back '.$workflow['data']['id'].' with id '.$wid;
            if (isset($workflow['webservice']) && $workflow['webservice']) {
                $results[] = 'Removing Webservice (MySQL): '.$workflow['webservice']['id'];
                $webservice->setId($workflow['webservice']['id'])->delete();
                foreach ($workflow['webservice'] as $key => $value) {
                    $method         = 'set'.ucfirst($key);
                    $webservice->$method($value);
                }
                $wid = $webservice->save();
                $results[] = 'Added Webservice (MySQL): '.$workflow['webservice']['id'].' with id '.$wid;
            }
            if (isset($workflow['listeners']) && $workflow['listeners']) {
                $results[] = 'Removing Listeners (MySQL): '.$workflow['listeners']['id'];
                $listeners->setId($workflow['listeners']['id'])->delete();
                foreach ($workflow['listeners'] as $key => $value) {
                    $method         = 'set'.ucfirst($key);
                    $listeners->$method($value);
                }
                $wid = $listeners->save();
                $results[] = 'Added Listener (MySQL): '.$workflow['webservice']['id'].' with id '.$wid;
            }
            if (isset($workflow['webservice_workflow']) && ($workflow['webservice_workflow'])) {
                $results[] = 'Removing Webservice Workflow (MySQL): '.$workflow['webservice_workflow']['id'];
                $webservice_workflow->setId($workflow['webservice_workflow']['id'])->delete();
                foreach ($workflow['webservice_workflow'] as $key => $value) {
                    $method         = 'set'.ucfirst($key);
                    $webservice_workflow->$method($value);
                }
                $wid = $webservice_workflow->save();
                $results[] =  'Added Webservice Workflow (MySQL): '.$workflow['webservice']['id'].' with id '.$wid;
            }
            foreach ($workflow['components'] as $id => $component) {
                $results[] = 'Removing Component (MongoDB): '.$id;
                $element->reset();
                $element->setId($id);
                $element->delete();
                foreach ($component as $key => $value) {
                    $method = 'set'.ucfirst($key);
                    $element->$method($value);
                }
                $wid = $element->save();
                $results[] = 'Adding Component (MongoDB): '.$id.' with id '.$wid['_id'];
            }
            $this->generate($workflow['data']['id'],$workflow['data']['namespace']);
            $results[] = 'Generated the workflow';
        } else {
            $results[] = 'Import Token Not Found, Aborting Import';
        }
        $results[] = 'Finished receiving a workflow';
        $results[] = '###########################################################';
        Log::warning($results);
        return json_encode($results);
    }

    /**
     * Will generate a workflow
     *
     * @param type $EVENT
     * @return boolean
     */
    protected function generate($id=false,$namespace=false) {
        $generated = false;
        if ($id && $namespace) {
            $generator = Humble::getHelper('paradigm/generator');
            $generator->setId($id);
            $generator->setNamespace($namespace);
            $generator->generate();
            $generated = true;
        }
        return $generated;
    }

    /**
     * Will enable a workflow
     *
     * @workflow use(process)
     * @param type $EVENT
     * @return boolean
     */
    public function enable($EVENT=false) {
        $enabled = false;
        if ($EVENT!==false) {
            $data   = $EVENT->load();
            if (isset($data['workflow_id'])) {

            }
            $enabled = true;
        }
        return $enabled;
    }

    /**
     * Will disable a workflow
     *
     * @workflow use(process)
     * @param type $EVENT
     * @return boolean
     */
    public function disable($EVENT=false) {
        $disabled = false;
        if ($EVENT!==false) {
            $data   = $EVENT->load();
            if (isset($data['workflow_id'])) {

            }
            $disabled = true;
        }
        return $disabled;
    }

    /**
     * Deletes a workflow and its related components
     */
    public function delete() {
        $id = $this->getId();
        if ($id) {
            $component      = Humble::getCollection('paradigm/elements');
            $diagram        = Humble::getEntity('paradigm/workflows')->setId($id);
            $webservice     = Humble::getEntity('paradigm/webservice_workflows');
            $listener       = Humble::getEntity('paradigm/workflow_listeners');
            $data           = $diagram->load();
            $workflow_id    = $data['workflow_id'];
            $workflow       = json_decode($data['workflow'],true);
            foreach ($workflow as $_id => $element) {
                $component->setId($element['id'])->delete();
            }
            $webservice->setWorkflowId($workflow_id)->delete(true);
            $listener->setWorkflowId($workflow_id)->delete(true);
            $diagram->delete();
        }
    }

}