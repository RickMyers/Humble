<?php
namespace Code\Framework\Paradigm\Models;
use Humble;
/**
 * Advocate for the paradigm workflow editor
 *
 * Performs actions on behalf of the paradigm workflow editor such as save, load
 *  generate, delete, configure, etc.
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Component
 * @package    Workflow
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Paradigm_Models_Manager.html
 * @since      File available since Version 1.0.1
 */
class Manager extends Model
{

    private $configs     = [
        'webservice' => true,
        'input' => true
    ];
    public function __construct()    {
        parent::__construct();
    }

    /**
     *
     * @return system
     */
    public function getClassName()    {
        return __CLASS__;
    }

    /**
     * Saves a workflow with associated information.  If ID is not passed, it does an add, otherwise save.
     *
     * @return type
     */
    public function save() {
        $workflow = Humble::entity('paradigm/workflows');
        $major_version = ($this->getMajorVersion()) ? $this->getMajorVersion() : '0';
        $minor_version = ($this->getMinorVersion()) ? $this->getMinorVersion() : '1';
        $workflow->setMajorVersion($major_version);
        $workflow->setMinorVersion($minor_version);
        $workflow->setCreator($this->getCreator());
        $workflow->setTitle($this->getTitle());
        if ($this->getPartial()) {
            $workflow->setPartial($this->getPartial());
        }
        $workflow->setWorkflow($this->getWorkflow());
        $workflow->setDescription($this->getDescription());
        $workflow->setImage($this->getImage());
        $workflow->setNamespace($this->getNamespace());
        $workflow->setSaved(date('Y-m-d H:i:s'));
        $id = $this->getId();
        if ($id) {
            $workflow->setId($id);
        }
        $id =    $workflow->save();
        
        return $id;
    }

    private function specialRoutes($element=[]) {
        
    }
    /**
     * Gets the configuration screen and also any pre-configured data
     *
     * What we are going to do is to get the specific instance information of this workflow component from Mongo
     * and add in information about the type of component from MySQL.  Depending on whether this component has been
     * configured we will go through one of two paths.  If not configured yet, then we show the basic component selection
     * screen where you can assign a method to the component.  If that has been done, then we go looking for any additional
     * configuration screens for this component.
     *
     * @return mixed
     */
    public function configureElement() {
        $id         = $this->getId();
        $element    = Humble::collection('paradigm/elements');
        $element->setId($id);
        $results    = $element->load();
        $window_id   = $this->getWindowId();
        $config     = null;
        $configURL  = false;
        //First, check to see if the element has some configuration data
        // if not, then load base configuration screen
        // else fetch detailed configuration data
        if ($results) {
            $args = array(
                'id' => $id,
                'window_id' => $window_id,
                'humble_session_id' => session_id()
            );            
            foreach ($results as $var => $val) {
                if ($var !== '_id') { //do we still need to do this? I still think so...
                    $args[$var] = $val;
                }
            }                
            if (isset($results['configured']) && $results['configured']) {
                $this->setResults($results);
                $element = Humble::entity('paradigm/workflow/components');
                //I might need to check if namespace, component, and method are set before doing a lookup.
                //If they aren't *all* set, just go right down to the switch statement... lemme think about that...
                $element->setNamespace(isset($results['namespace']) ? $results['namespace'] : null);
                $element->setComponent(isset($results['component']) ? $results['component'] : false);
                $element->setMethod(isset($results['method']) ? $results['method'] : false);
                $data    = $element->load(true);
                $this->setData($data);
                $this->setElement($element);
            }
            //$data = $this->specialRoutes($data);
            if (isset($data['configuration']) && $data['configuration']) {
                    $configURL = $data['configuration'];
                    $configURL = (substr($configURL,0,1)=='/') ? $configURL : '/'.$configURL;
                    //now do the manual configuration screen fetch passing all relative parameters and the settings

                    $call = ['method'=>'POST','url'=>$configURL,'blocking'=>false,'CURL'=>true, 'arguments' =>['namespace','id','window_id','data','component','method']];
                    $config = $this->_hurl($configURL,$args,$call,true,false,false);
                } else {
                    if ((isset($results['configured']) && (!$results['configured'])) || (isset($this->configs[$results['type']]))) {
                        foreach ($args as $arg => $val) {
                            $setter = 'set'.$this->underscoreToCamelCase($arg,true);
                            $this->$setter($val);
                        }
                        $config  = $results['type'].'Configuration';
                        if (!isset($results['namespace']) || !$results['namespace']) {
                            $element->setNamespace($this->getNamespace())->save();      //if you don't have a namespace yet (webhook, webservice, etc, this will assign you the current namespace of the workflow you are in
                            $results['namespace'] = $this->getNamespace();              //Everything has got to belong to something
                        }
                        $this->setData(json_encode($results));
                        $config = $this->$config();                    
                    }
                }            
        }
        return $config;
    }

    /**
     *
     */
    public function removeElement() {
        $element = Humble::collection('paradigm/elements');
        $element->setId($this->getId());
        if ($data = $element->load()) {
            //######################################################################
            //
            // Depending on type of component we are deleting, some additional steps
            //  may be necessary
            //
            //######################################################################
            switch ($data['type']) {
                case    'webservice'    :
                     $service    = Humble::entity('paradigm/webservices');
                     $service->setWebserviceId($data['_id'])->load(true);
                     if ($service->getUri() && $service->getId()) {
                        Humble::entity('paradigm/webservice_workflows')->setWebserviceId($service->getId())->delete(true);
                        $service->delete();
                     }
                    break;
                default                 :
                    break;
            }
            $element->delete();
        }
    }

    /**
     *
     */
    public function updateElement() {
        $settings = json_decode($this->getData(),true);
        $element  = Humble::collection('paradigm/elements');
        $this->setWindowId($settings['window_id']);
        $element->setId($settings['id']);
        if ($data = $element->load()) {
            foreach ($settings as $key => $val) {
                if ($key == 'window_id') {
                    continue;
                }
                $method = 'set'.ucfirst($key);
                $element->$method($val);
            }
            $element->setConfigured(true);
            $element->save();
        }
    }

    public function run() {
        if ($workflow_id = $this->getId()) {
            $workflowRC    = 0;
            $cancelBubble  = false;
            $EVENT = \Event::get('TestEvent',[]);
            if (file_exists('Workflows/'.$workflow_id.'.php')) {
                include 'Workflows/'.$workflow_id.'.php';
            }
        }
    }
    
    /**
     * Will take a JSON construct representing a workflow and copy it's contents into the local MongoDB and MySQL instances
     *
     */
    public function import() {

    }

    /**
     * Will generate the JSON construct required to import a workflow
     */
    public function export() {

    }

}
