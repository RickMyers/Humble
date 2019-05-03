<?php
namespace Base\Humble\Models;
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
 * @author     Rick Myers <rick@enicity.com>
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Humble_Models_System.html
 * @since      File available since Version 1.0.1
 */
class System extends Model
{
    use \Base\Humble\Event\Handler;

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

    public function save() {
        $root       = Environment::getRoot('core');
        $rain       = Environment::getInternalTemplater($root.'/lib/sample/install','xml');
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
        file_put_contents('../application.xml',$rain->render('application',true));
        Humble::response('Saved...');
    }

    /**
     *
     *
     * @return type
     */
    public function _landing() {
        $status = Environment::status();
        return $status->landing;
    }

    /**
     * Returns whether the system is in the process of shutting down
     *
     * @return integer
     */
    public function isQuiescing() {
        if (!$this->xml) {
            $this->xml  = Environment::status();
        }
        return $this->xml->status->quiescing;
    }

    /**
     * Returns the current status of the system
     *
     * @return integer
     */
    public function isActive() {
        if (!$this->xml) {
            $this->xml  = Environment::status();
        }
        return $this->xml->status->enabled;
    }

    /**
     * Sets the system quiescing bit...
     *
     *
     */
    public function quiesce() {
        $xml  = Environment::status();
        $xml->status->quiescing = $this->getValue();
        file_put_contents('../application.xml',$xml->asXML());
    }

    /**
     * Sets the system offline bit...
     *
     *
     */
    public function online() {
        $xml  = Environment::status();
        $xml->status->enabled = 1;
        file_put_contents('../application.xml',$xml->asXML());
    }

    /**
     * Sets the system offline bit...
     *
     *
     */
    public function offline() {
        $xml  = Environment::status();
        $xml->status->enabled = 0;
        file_put_contents('../application.xml',$xml->asXML());
    }

    /**
     * Returns TRUE if SSO (Single Sign On) is enabled
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function SSOEnabled($EVENT=false) {
        $application   = Environment::status();
        return ($application && (isset($application->status->SSO)) && ($application->status->SSO->enabled == 1));

    }

}