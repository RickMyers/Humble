<?php
namespace Code\Base\Workflow\Models;
use Humble;
/**
 * Contains the communication options
 *
 * Emails, texts, etc, from a workflow
 *
 * PHP version 5.5+
 *
 * LICENSE: http://www.enicity.com/license.txt
 *
 * @category   Component
 * @package    Application
 * @author     Original Author <author@example.com>
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.0.0
 */
class Notification extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * All Classes are requried to have this method
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Sends a desktop notification/alert
     *
     * @workflow use(notification) authorization(false) configuration(/workflow/notification/alert)
     */
    public function alert($EVENT=false) {
        $alerted = false;
        $string  = Humble::getHelper('core/string');
        if ($EVENT) {
            $mydata = $EVENT->fetch();
            $alert = $string->translate($mydata['message'],$EVENT->load());
            $EVENT->alert($alert);
            $alerted = true;
        }
        return $alerted;
    }

    /**
     * Sends a desktop notification/alert
     *
     * @workflow use(notification) configuration(/workflow/notification/dashboard)
     */
    public function dashboard($EVENT=false) {
        $alerted = false;
        if ($EVENT) {
            $mydata = $EVENT->fetch();
            //do something
            $alerted = true;
        }
        return $alerted;
    }

    /**
     * Sends an email
     *
     * @workflow use(notification) authorization(false) configuration(/workflow/notification/email)
     */
    public function email($EVENT=false) {
        $emailed = false;
        if ($EVENT) {
            $data   = $EVENT->load();   //get the original event data, it should have information by now
            $mydata = $EVENT->fetch();
            if (isset($data['user']) && isset($data['user']['email']) && ($data['user']['email'])) {
                $emailed = $this->sendEmail($data['user']['email'],$mydata['email_description'],Humble::string($mydata['email_template']));
            }
        }
        return ($emailed !== false);
    }

    /**
     * Sends a text, please don't do this while driving
     *
     * @workflow use(notification) authorization(false) configuration(/workflow/notification/text)
     */
    public function text($EVENT=false) {
        $texted = false;
        if ($EVENT) {
            $mydata = $EVENT->configurations[$EVENT->_target()];
            if (isset($mydata['number']) && isset($mydata['message'])) {
                $this->setAction('getBalance');
                $network = $this->getPhoneNetworkInformation();
                $number = str_replace(array(" ","-","."),array('','',''),$mydata['number']);
                //mail($number.'@vtext.com','',$mydata['message']);
                $texted = true;
            }
        }
        return $texted;
	}

    /**
     * Returns a message as a response, through the response method on our Humble factory
     *
     * @workflow use(notification) configuration(/workflow/notification/response)
     * @param type $EVENT
     */
    public function response($EVENT=false) {
        $responded = false;
        if ($EVENT) {
            $mydata = $EVENT->fetch();
            if (isset($mydata['response'])) {
                Humble::response($mydata['response']);
                $responded = true;
            }
        }
        return $responded;
    }

}
?>