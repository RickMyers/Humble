<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Environment;
/**    
 * System related functions
 *
 * see title
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Humble_Models_System.html
 * @since      File available since Version 1.0.1
 */
class System extends Model
{
    use \Code\Framework\Humble\Traits\EventHandler;

    private $xml = false;
    

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

    public function status() {
        return Environment::status();
    }
    
    /**
     * 
     */
    public function save() {
        $root       = Environment::getRoot('humble');
        $rain       = Environment::getInternalTemplater($root.'/lib/sample/install','xml');
        $rain->assign('state',      (($this->getState())         ? $this->getState() : 'DEVELOPMENT'));
        $rain->assign('enabled',    (($this->getEnabled())       ? 1 : 0));
        $rain->assign('installer',  (($this->getInstaller())     ? 1 : 0));
        $rain->assign('authorized', (($this->getAuthorization()) ? 1 : 0));
        $rain->assign('polling',    (($this->getPolling()) ? 1 : 0));
        $rain->assign('interval',   (($this->getInterval())      ? $this->getInterval()  : 15));
        $rain->assign('landing',    (($this->getLanding())       ? $this->getLanding()   : ""));
        $rain->assign('login',      (($this->getLogin())         ? $this->getLogin()     : ""));
        $rain->assign('logout',     (($this->getLogout())        ? $this->getLogout()    : ""));
        $rain->assign('quiescing',  (($this->getQuiescing())     ? $this->getQuiescing() : ""));
        $rain->assign('SSO',        (($this->getSso())           ? $this->getSso()       : ""));
        $rain->assign('version',    (($this->getVersion())       ? $this->getVersion()   : "0.0.0.0.1"));
        $rain->assign('engine',     (($this->_isLinux())         ? `which php.exe` : 'c:\\php\\'));
        $rain->assign('support_name',(($this->getSupportName())  ? $this->getSupportName() : ""));
        $rain->assign('support_email',(($this->getSupportEmail())? $this->getSupportEmail() : ""));
        file_put_contents(Environment::applicationXMLLocation(),$rain->render('application',true));
        Humble::response('Saved...');
    }

    /**
     * Records the SMTP settings into the generic system variables tables, and should be recorded as secrets
     * 
     * @return string
     */
    public function smtpSettings() {
        $message = "Error, was not able to save SMTP settings";
        $host = $this->getHost();
        $name = $this->getUserName();
        $pwd  = $this->getPassword();
        $ns   = $this->getNamespace();
        if ($host && $name && $pwd) {
            $settings = Humble::entity('humble/system/variables');
            $x = $settings->setNamespace($ns)->setVariable('SMTP_Host')->setValue($host)->save();
            $y = $settings->reset()->setNamespace($ns)->setVariable('SMTP_Username')->setValue($name)->save();
            $z = $settings->reset()->setNamespace($ns)->setVariable('SMTP_Password')->setValue($pwd)->save();
            if ($x && $y && $z) {
                $message = "SMTP Settings Saved!";
            }
        }
        return $message;
    }
    /**
     *
     *
     * @return type
     */
    public function _landing() {
        $status = Environment::status();
        return $status['landing'];
    }

    /**
     * Returns whether the system is in the process of shutting down
     *
     * @return integer
     */
    public function isQuiescing() {
        if (!$this->xml) {
            $this->xml  = Environment::status(true);
        }
        return $this->xml['status']['quiescing'];
    }

    /**
     * Returns the current status of the system
     *
     * @return integer
     */
    public function isActive() {
        if (!$this->xml) {
            $this->xml  = Environment::status(true);
        }
        return $this->xml['status']['enabled'];
    }
    
    /**
     * Returns the SDLC of the app...
     * 
     * @TODO: Use the order attribute to pre-sort
     * 
     * @return array
     */
    public function stages() {
        $stages = [];
        $this->xml = $this->xml ? $this->xml : Environment::status(true);
        foreach (($this->xml['stages'] ?? []) as $stage => $attr) {
            $order = $attr['@attributes']['order'] ?? '1';
            $stages[(int)$order] = $stage;
        }
        ksort($stages);
        return $stages;
    }
    
    /**
     * If available, returns the current state or the default DEVELOPMENT if not set
     * 
     * @return string
     */
    public function state() {
        $this->xml = $this->xml ? $this->xml : Environment::status(true);
        return (isset($this->xml['state'])) ? $this->xml['state'] : 'DEVELOPMENT';
    }
    
    /**
     * Changes the state, values are DEVELOPMENT, PRODUCTION, and DEBUG
     */
    public function changeState() {
        $xml = Environment::applicationXML();
        $xml->state = $this->getState();
        file_put_contents(Environment::applicationXMLLocation(),$xml->asXML());
        Environment::recacheApplication();
    }
    
    /**
     * Sets the system quiescing bit...
     *
     *
     */
    public function quiesce() {
        $xml = Environment::applicationXML();
        $xml->status->quiescing = $this->getValue();
        file_put_contents(Environment::applicationXMLLocation(),$xml->asXML());
    }

    /**
     * Sets the system offline bit...
     *
     *
     */
    public function online() {
        $xml = Environment::applicationXML();
        $xml->status->enabled = 1;
        file_put_contents(Environment::applicationXMLLocation(),$xml->asXML());
    }

    /**
     * Sets the system offline bit...
     *
     *
     */
    public function offline() {
        $xml = Environment::applicationXML();
        $xml->status->enabled = 0;
        file_put_contents(Environment::applicationXMLLocation(),$xml->asXML());
    }

    /**
     * Returns TRUE if SSO (Single Sign On) is enabled
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function SSOEnabled($EVENT=false) {
        $application =  Environment::status(true);
        return ($application && (isset($application['status']['SSO'])) && ($application['status']['SSO']['enabled'] == 1));

    }

    /**
     * Triggers the re-caching to memcache of the application
     */
    public function recache() {
        Environment::recacheApplication();
    }
    
    /**
     * System Alert handler, allows for handling system wide alerts through workflows
     * 
     * 
     * @workflow use(EVENT) emit(SystemAlert)
     * @param type $type
     * @param type $data
     */
    public function systemAlert($type=false,$data) {
        if ($type) {
            $this->trigger('SystemAlert',__CLASS__,__METHOD__,['type'=>$type,'details'=>$data]);
        }
    }
    
    /**
     * System Notification handler, allows for handling system wide notifications through workflows
     * 
     * 
     * @workflow use(EVENT) emit(SystemNotification)
     * @param type $type
     * @param type $data
     */
    public function systemNotification($type=false,$data) {
        if ($type) {
            $this->trigger('SystemNotification',__CLASS__,__METHOD__,['type'=>$type,'details'=>$data]);
        }
    }    
    
    /**
     * Gets the flag section of the application configuration file
     * 
     * @return array
     */
    public function flags() {
        return Environment::getApplication('flags');
    }
    
    /**
     * Sets the state of a flag in the application configuration file
     */
    public function setFlagState() {
        if ($flag    = $this->getFlag()) {
            $application = Environment::applicationXML();
            $application->flags->$flag = ($this->getState() == 'On') ? 1 : 0;
            file_put_contents(Environment::applicationXMLLocation(),$application->asXML());
            Environment::recacheApplication();
        }
    }
    
    /**
     * Goes through the cache and checks to see if everything that should be cached is cached, and generates a report of actions
     * 
     * @return array
     */
    public function cacheCheck() {
        $stats = [
            "modules" => [
                "score" => 0,
                "count" => 0,
                "grade" => 0,
                "errors" => []
            ],
            "controllers" => [
                "score" => 0,
                "count" => 0,
                "grade" => 0,
                "errors" => []
            ],
            "entities" => [
                "keys" => [
                    "score" => 0,
                    "count" => 0,
                    "grade" => 0,
                    "errors" => []
                ],
                "cols" => [
                    "score" => 0,
                    "count" => 0,
                    "grade" => 0,
                    "errors" => []
                ]
            ],
            "metadata" => [
                "score" => 0,
                "count" => 0,
                "grade" => 0,
                "errors" => []
            ],
        ];
        foreach (Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            $stats["modules"]['count']++;
            $stats["modules"]['score'] += ($result = Humble::cache('module-'.$module['namespace'])) ? 1 : 0;
            if (!$result) {
                $stats['modules']['errors'][] = $key;
            }            
            if ($cdh = dir('Code/'.$module['package'].'/'.$module['controller'])) {
                while ($entry = $cdh->read()) {
                    if (($entry == '.') || ($entry == '..') || ($entry === 'Cache')) {
                        continue;
                    }
                    $stats['controllers']['count']++;
                    $key = 'controller-'.$module['namespace'].'/'.str_replace('.xml','',$entry);
                    $stats["controllers"]['score'] += ($result = Humble::cache($key)) ? 1 : 0;
                    if (!$result) {
                        $stats['controllers']['errors'][] = $key;
                    }
                }
                $config = \Humble::config($module['namespace']);
                foreach ($config->orm->entities as $entities) {
                    foreach ($entities as $entity => $options) {
                        $stats['entities']['keys']['count']++;
                        $stats['entities']['cols']['count']++;
                        $key = 'entity_keys-'.$module['namespace'].'/'.$entity;
                        $stats["entities"]['keys']['score'] += ($result = Humble::cache($key)) ? 1 : 0;
                        if (!$result) {
                            $stats['entities']['keys']['errors'][] = $key;
                        }
                        $key = 'entity_columns-'.$module['namespace'].'/'.$entity;
                        $stats["entities"]['cols']['score'] += ($result = Humble::cache($key)) ? 1 : 0;
                        if (!$result) {
                            $stats['entities']['cols']['errors'][] = $key;
                        }
                    }
                }
                
                $stats['metadata']['score'] += (Humble::cache('public_routes')) ? 1 : 0; $stats['metadata']['count']++;
                $stats['metadata']['score'] += (Humble::cache('application'))   ? 1 : 0; $stats['metadata']['count']++;
                $stats['metadata']['score'] += (Humble::cache('project'))       ? 1 : 0; $stats['metadata']['count']++;
                $stats['metadata']['score'] += (Humble::cache('api_policy'))    ? 1 : 0; $stats['metadata']['count']++;
            }
        }
        $stats['modules']['grade'] = round(($stats['modules']['score']/$stats['modules']['count'])*100);
        $stats['controllers']['grade'] = round(($stats['controllers']['score']/$stats['controllers']['count'])*100);
        $stats['metadata']['grade'] = round(($stats['metadata']['score']/$stats['metadata']['count'])*100);
        $stats['entities']['keys']['grade'] = round(($stats['entities']['keys']['score']/$stats['entities']['keys']['count'])*100);
        $stats['entities']['cols']['grade'] = round(($stats['entities']['cols']['score']/$stats['entities']['cols']['count'])*100);
        return $stats;    
    }
    
}