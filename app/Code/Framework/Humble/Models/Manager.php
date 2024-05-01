<?php
namespace Code\Framework\Humble\Models;
use Environment;
use Humble;
use Log;
/** 
 * General manager class
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Other
 * @since      File available since Release 1.0.0
 */
class Manager extends Model
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
     * Returns a unique identifier to help identify request threads when you come from a client who might have the site open in multiple tabs
     * 
     * @return string
     */
    public function browserTabId() {
        $_SESSION['BROWSER_TABS'][$tab_id = $this->_token(6)] = '';
        return $tab_id;
    }
    
    /**
     * Used to foil cross-site request forgeries.   A combination of the tab_id token and the CSRF token will be used to make sure the request is kosher
     * 
     * @param string $tab_id
     * @return string
     */
    public function csrfBuster($tab_id) {
        return $_SESSION['BROWSER_TABS'][$tab_id] = $this->_token(6);
    }
    
    /**
     * Based upon the input JSON data, execute 1-* actions and return the results as a single coherent array
     *
     * @return array
     */
    public function poll() : array {
        $data   = $this->getArguments();
        $p_uid  = $this->getSessionUserId();                                    //p_uid = passed in user id
        $uid    = $this->getUserId();
//        if ($p_uid !== $uid) {
            //Humble::emit('logUserOff',['uid'=>$p_uid]);
 //           die('[]');
  //      }
        if ($data) {
            $data = json_decode($data);
            if ($data) {
                foreach ($data as $var => $val) {
                    $method = 'set'.ucfirst($var);
                    $this->$method($val);
                }
            }
        }
        $results    = [];
        $original   = $this->_namespace();
        $beats      = json_decode($this->getBeats(),true);
        $this->setSessionId(true);                                              //transfer session id
        foreach ($beats as $id => $beat) {
            $this->_namespace($beat['namespace']);
            $method       = $beat['resource'];
            $results[$id] = $this->$method();
        }
        $this->_namespace($original);
        return $results;
    }

    protected function updateConfig($project) {
        $xml = simplexml_load_file('Code/'.$project->package.'/'.$project->module.'/etc/config.xml');
        if (!isset($xml->{$project->namespace}->orm->entities->users)) {
            $xml->{$project->namespace}->orm->entities->addChild('users');
            $xml->{$project->namespace}->orm->entities->addChild('user_identification');
            $xml->{$project->namespace}->orm->entities->user_identification->addAttribute('polyglot','Y');
        }
        return file_put_contents('Code/'.$project->package.'/'.$project->module.'/etc/config.xml',$xml->asXML());
    }
    
    /**
     * Loads the default module with a few classes and tables, but overrides some things
     * 
     * @return $this
     */
    public function tailorSystem($project) {
        $sources = [
            'Controllers' => 'Code/Framework/Humble/lib/sample/install/Controllers',
            'Models'      => 'Code/Framework/Humble/lib/sample/install/Models',
            'Schema'      => 'Code/Framework/Humble/lib/sample/install/Schema/Update',
            'Entities'    => 'Code/Framework/Humble/lib/sample/install/Entities'
        ];
        $dest = [
            'Controllers' => 'Code/'.$project->package.'/'.$project->module.'/Controllers',
            'Models'      => 'Code/'.$project->package.'/'.$project->module.'/Models',
            'Schema'      => 'Code/'.$project->package.'/'.$project->module.'/Schema/Update',
            'Entities'    => 'Code/'.$project->package.'/'.$project->module.'/Entities',
        ];
        $srch    = ['&&NAMESPACE&&','&&PACKAGE&&','&&MODULE&&'];
        $repl    = [$project->namespace,$project->package,$project->module];
        foreach ($sources as $component => $location) {
            print("Processing ".$component."\n");
            $dh = dir($location);
            while ($entry = $dh->read()) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                file_put_contents($dest[$component].'/'.$entry,str_replace($srch,$repl,file_get_contents($sources[$component].'/'.$entry)));
            }
        }
        return $this->updateConfig($project);
    }
    
    /**
     * Maybe expand this for the other index/404 actions as well as home page?
     * 
     * @param object $project
     * @return $this
     */
    public function createLandingPage($project='') {
        if ($parts = explode('/',$project->landing_page)) {
            $util = Humble::model('admin/utility');
            //TODO: Pass engine in...
            $util->setDescription($this->getDescription())->setActionDescription($this->getActionDescription())->setNamespace($parts[1])->setEngine('Twig')->setName($parts[2])->setAction($parts[3])->createController(true,true);
        }
        return $this;
    }    
}
