<?php
namespace Code\Base\Workflow\Models;
use Humble;
/**
 * Contains the communication options
 *
 * Emails, texts, etc, from a workflow
 *
 * PHP version 7.2+
 *
 * LICENSE: http://www.humbleprogramming.com/license.txt
 *
 * @category   Component
 * @package    Base
 * @author     Original Author <rick@humbleprogramming.com>
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

    public function substitute($text,$values) {
        $retval = '';
        foreach (explode('%%',$text) as $idx => $section) {
            if ($idx%2 != 0) {
                if (strpos($section,'.')) {
                    $s = '$values';
                    foreach (explode('.',$section) as $node) {
                        $s .= "['".$node."']";
                    }
                    eval('$valid = isset('.$s.');');
                    if ($valid) {
                        eval('$retval .='.$s.';');                              //Yes, it is evil, but what else are you going to do?
                    } else {
                        $retval .= '';
                    }
                } else {
                    $retval .= isset($values[$section]) ? $values[$section] : '';
                }
            } else {
                $retval .= $section;
            }
        }
        return $retval;
    }

    /**
     * Sends a desktop notification/alert
     *
     * @workflow use(notification) authorization(false) configuration(/workflow/notification/alert)
     */
    public function alert($EVENT=false) {
        $alerted = false;
        $string  = Humble::getHelper('humble/string');
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
     * Sends an email, the recipients can be a value entered by the configuration page, or a field on the event could contain the recipients.  The same is true for subject and message.
     *
     * @workflow use(notification) configuration(/workflow/notification/email)
     */
    public function email($EVENT=false) {
        $emailed = false;
        if ($EVENT) {
            $data   = $EVENT->load();   //get the original event data, it should have information by now
            $cfg    = $EVENT->fetch();
            if ($recipients = isset($cfg['recipient_type']) && ($cfg['recipient_type']=='value') ? $cfg['recipients'] : ((isset($data[$cfg['recipients']]) && $data[$cfg['recipients']]) ? $data[$cfg['recipients']] : false)) {
                $subject    = isset($cfg['subject_type']) && ($cfg['subject_type']=='value')   ? $cfg['subject'] : ((isset($data[$cfg['subject']]) && $data[$cfg['subject']]) ? $data[$cfg['subject']] : '');
                $from       = isset($cfg['from_type'])    && ($cfg['from_type']=='value')      ? $cfg['from']     : ((isset($data[$cfg['from']])    && $data[$cfg['from']])    ? $data[$cfg['from']]    : false);
                $message    = $this->substitute(isset($cfg['message_field']) && ($cfg['message_field'] && isset($data[$cfg['message_field']]))  ? $data[$cfg['message_field']] : $cfg['email_message'],$data);
                if (count($recipients = explode(';',$recipients)) && $message) {
                    $emailed = Humble::getHelper('humble/email')->sendEmail($recipients,$subject,$message,$from);
                }

            }

        }
        return ($emailed !== false);
    }

    /**
     * Sends a text, please don't do this while driving
     *
     * @workflow use(notification) configuration(/workflow/notification/text)
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