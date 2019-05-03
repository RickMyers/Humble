<?php
namespace Base\Paradigm\Helpers;
use Humble;
/**    
 *
 * Workflow Generator
 *
 * Compiles a JSON based workflow, designed using the Paradigm Workflow
 * Editor and creates the representative PHP program
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Workflow
 * @author     Rick Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Paradigm_Helpers_Generator.html
 * @since      File available since Version 1.0.1
 */
class Generator extends Helper
{

    private $components         = [];
    private $_workflowId        = false;
    private $start              = false;
    private $title              = '';
    private $description        = '';
    private $version            = '';
    private $trigger            = false;
    private $workflow           = "";
    private $header             = <<<HDR
<?php
/* ###################################################################################
  __          __        _     __ _
  \ \        / /       | |   / _| |
   \ \  /\  / /__  _ __| | _| |_| | _____      __
    \ \/  \/ / _ \| '__| |/ /  _| |/ _ \ \ /\ / /
     \  /\  / (_) | |  |   <| | | | (_) \ V  V /
      \/  \/ \___/|_|  |_|\_\_| |_|\___/ \_/\_/
            
&&NAME&&, &&VERSION&&

&&DESCRIPTION&&

Copyright Humble Project, 2014-Present, all rights reserved
##################################################################################### */
HDR;

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
     *
     */
    protected function traverse($node) {
        // o get the mongo information
        // o make sure it is set/configured, else will need to error off
        // o punch out the basic information
        // o $obj->$method(Event::get(EVENT,this_element_id);   This will load the configuration data stored in mongo into the event object as it's own node,
        //   and then passed-in/returned to the element method after having set what the current id is in the event.  The event ID will change as we progress down the workflow
        global $tabs;
        $debug = false;
        $includeBranch = true;  //Do I include the "goto" or "branch" to next statement
        if (!$node) {
            \Log::console('Got a null one');
            return true;
        }
        $exclude = [            //these don't have normal setups
            'begin'     => true,
            'terminus'  => true,
            'detector'  => true,
            'operation' => true,
            'sensor'    => true,
            'system'    => true
        ];
        if (!isset($exclude[$node['element']])) {
            if (isset($node['configuration'])) {
                if (!isset($node['configuration']['namespace']) || !($node['configuration']['namespace'])) {
                    throw new \Exceptions\IncompleteConfigurationException('A namespace has not been set for '.$node['text'].' ['.$node['element'].']',12);
                }
                if (!isset($node['configuration']['method']) || !($node['configuration']['method'])) {
                    throw new \Exceptions\IncompleteConfigurationException('The method has not been set for '.$node['text'].' ['.$node['element'].']',12);
                }
                if (!isset($node['configuration']['component']) || !($node['configuration']['component'])) {
                    throw new \Exceptions\IncompleteConfigurationException('A component has not been set for '.$node['text'].' ['.$node['element'].']',12);
                }
            } else {
                throw new \Exceptions\UnconfiguredException($node['text']." [".$node['element'].'] has not been configured',12);
            }
        } else if (($node['element']=='terminus')) {
            if (!(isset($node['configuration'])) || ($node['configuration']['configured']==0) ) {
                if ($debug) {
                    \Log::console($node);
                }
                throw new \Exceptions\UnconfiguredException($node['text']." [".$node['element'].'] has not been configured',12);
            }
        }
        $cnf = $node['configuration'];
        if (isset($node['punched']) && $node['punched']) {
            return;   //what this says is the code/label for this one is already output, so don't generate and output it again
        }
        $this->components[$node['id']]['punched'] = true;

        $this->workflow .= $tabs.(($node['element']==='begin') ? "workflow" : "label")."_".$node['id'].":\n";
        switch ($node['element']) {
            case "external"     :
                $pwf = Humble::getEntity('paradigm/workflows')->setId($cnf['partial-workflow'])->load();
                $this->workflow .= "\n".$tabs."include 'Workflows/".$pwf['workflow_id'].".php';\n\n";
                foreach ($node['connectors'] as $direction) {
                    if (isset($direction['begin']) && (isset($direction['begin']['from'])) && (isset($direction['begin']['from']['id']))) {
                        $this->workflow .= $tabs."goto label_".$direction['begin']['to']['id'].";\n";
                        $this->traverse($this->components[$direction['begin']['to']['id']]);
                    }
                }
                break;            
            case "decision"     :
                $this->workflow .= $tabs.'if (Humble::getModel("'.$cnf['namespace'].'/'.$cnf['component'].'")->'.$cnf['method'].'(Event::set($EVENT,"'.$node['id'].'"))) {'."\n";
                $tabs .= "\t";
                $this->workflow .= $tabs."goto label_".$node['connectors']['E']['begin']['to']['id'].";\n";
                $this->traverse($this->components[$node['connectors']['E']['begin']['to']['id']]);
                $tabs = substr($tabs,0,strlen($tabs)-1);
                $this->workflow .= $tabs."} else {\n";
                $tabs .= "\t";
                $this->workflow .= $tabs."goto label_".$node['connectors']['S']['begin']['to']['id'].";\n";
                $this->traverse($this->components[$node['connectors']['S']['begin']['to']['id']]);
                $tabs = substr($tabs,0,strlen($tabs)-1);
                $this->workflow .= $tabs."}\n";
                break;
            case "terminus"     :
                if (isset($cnf['cancel']) && ($cnf['cancel']=='Y')) {
                    $this->workflow .= $tabs.'$cancelBubble = true;'."\n";
                } else {
                    $this->workflow .= $tabs.'$cancelBubble = false;'."\n";
                }
                $this->workflow .= $tabs.'$workflowRC = '.($cnf['returns'] ? 'true':'false').";\n";
                $this->workflow .= $tabs."//END OF WORKFLOW BRANCH\n";
                break;
            case "actor"        :
            case "sensor"       :
            case "webservice"   :
            case "trigger"      :
            case "system"       :
                $this->trigger = $cnf;
            case "begin"        :
                //do variable substitution stuff and print the header
                $tabs .= "\t";   //lets indent it!
                $includeBranch = false;  //No need to include the goto since I'm at the start
            case "operation"    :
                //We have the namespace, method and component set in the operation.tpl configuration page
                
            default             :
                if ($includeBranch) {
                    $this->workflow .= $tabs.'Humble::getModel("'.$cnf['namespace'].'/'.$cnf['component'].'")->'.$cnf['method'].'(Event::set($EVENT,"'.$node['id'].'"));'."\n";
                }
                foreach ($node['connectors'] as $direction) {
                    if (isset($direction['begin']) && (isset($direction['begin']['from'])) && (isset($direction['begin']['from']['id']))) {
                        if ($includeBranch) {
                            $this->workflow .= $tabs."goto label_".$direction['begin']['to']['id'].";\n";
                        }
                        $this->traverse($this->components[$direction['begin']['to']['id']]);
                    }
                }
                break;
        }
        return true;
    }

    /**
     * Loads the workflow into a useable state in memory and fetches the currently configured state.
     * If there is an element that is not configured, this will return false and generation will stop
     *
     * @param boolean $workflow
     * @return boolean
     */
    protected function preProcess($workflow=false) {
        if ($workflow) {
            foreach ($workflow as $element) {
                $configuration = Humble::getCollection('paradigm/elements');
                $element['configuration'] = $configuration->setId($element['id'])->load();
                $this->components[$element['id']] = $element;
                if ($element['element']!=='connector') {
                   // \Log::console($element);
                }
                switch ($element['element']) {
                    case "diagramlabel" :
                        $this->title = $element['text'];
                        break;
                    case "begin"        :
                        $this->start = $element;
                        $this->_workflowId($element['id']);
                        break;
                    default             :   break;
                }
            }
            $workflow = true;
        }
        $srch = array('&&NAME&&','&&VERSION&&','&&DESCRIPTION&&');
        $repl = array($this->title,$this->version,$this->description);
        $this->workflow .= str_replace($srch,$repl,$this->header)."\n";
        return $workflow;
    }

    /**
     * This converts a diagram into a PHP program.  It can take input via post, or if not post, then retrieves the diagram from the DB to generate
     *
     * @return type
     */
    public function generate() {
        global $tabs;
        $tabs        = '';  //Keeps track of indentation... the number of "tabs" to use...
        $diagram     = $this->getWorkflow();
        $workflow    = Humble::getEntity("paradigm/workflows");
        $workflow->setId($this->getId());
        $data        = $workflow->load();
        $namespace   = $this->getNamespace();
        if ($diagram || (isset($data['workflow']) && $data['workflow'])) {
            $this->title        = $data['title'];
            $this->description  = $data['description'];
            $this->version      = $data['major_version'].'.'.$data['minor_version'];
            $diagram            = ($diagram) ? $diagram : $data['workflow'];
            $this->setJson($diagram);
            if ($this->preProcess(json_decode($diagram,true))) {
                if ($this->traverse($this->start)) {
                    if ($this->trigger) {
                        //only if this is a trigger caused by an actor or something that hits a URL, not a system thrown event (time based)
                        if (isset($this->trigger['namespace']) && isset($this->trigger['component']) && isset($this->trigger['method'])) {
                            $event = Humble::getEntity('paradigm/workflow_listeners');
                            $event->setWorkflowId($this->_workflowId());
                            $trigger = $event->load(true);
                            if (!$trigger) {
                                //this hasn't been set up yet
                                $event->setNamespace($this->trigger['namespace']);
                                $event->setComponent($this->trigger['component']);
                                $event->setMethod($this->trigger['method']);
                                $event->save();
                            }
                        }
                    }
                    $workflow->setGeneratedWorkflowId($this->_workflowId());
                    $workflow->setGenerated(date('Y-m-d H:i:s'));
                    $workflow->save();
                }
            }
        }
        @mkdir('Workflows',0775);
        file_put_contents("Workflows/".$this->_workflowId().".php",$this->workflow);
        return $this->workflow;
    }

    /**
     * The workflow ID is the unique ID associated to the START glyph in the chart.  It is the entry point for the workflow, which can be used in the future for offpage connectors
     *
     * @param type $id
     * @return type
     */
    public function _workflowId($id=false) {
        if ($id) {
            $this->_workflowId = $id;
        } else {
            return $this->_workflowId;
        }
    }
}