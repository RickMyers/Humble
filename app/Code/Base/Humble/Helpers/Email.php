<?php
namespace Code\Base\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * E-mail related functionality
 *
 * Email related mechanics
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Other
 * @author     Rick Myers 
 * @license    https://humblecoding.com/license.txt
 * @version    1.0.0
 * @link       https://humblecoding.com/docs/class-Email.html
 * @since      File available since Release 1.0.0
 */
class Email extends Helper
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
     * Sends an email through the default send method of the platform
     *
     * @param mixed $to
     * @param string $subject
     * @param string $body
     * @return boolean
     */
    public function sendEmail($to=false,$subject=false,$body=false,$from=false,$reply=false,$attachment=false) {
        $to       = (is_string($to)) ? [$to] : $to;
        $settings = Environment::settings();
        $project  = Environment::getProject();
        $prj      = explode(':',(string)$project->project_url);
        $from = ($from ? $from : 'webmaster@'.substr($prj[1],2));
        $reply = ($reply ? $reply : 'noreply@'.substr($prj[1],2));
        $mailer = new \PHPMailer;
        $mailer->isSMTP();
        $mailer->Host = $settings->getSmtpHost();
        $mailer->SMTPAuth = true;
//        $mailer->SMTPDebug = 2;
        $mailer->Username = $settings->getSmtpUserName();
        $mailer->Password = $settings->getSmtpPassword();
        $mailer->SMTPSecure = 'tls';
        $mailer->setFrom($from,'');
        foreach ($to as $recipient) {
            $mailer->addAddress($recipient);
        }
        $mailer->Subject = $subject;
        $mailer->addReplyTo($reply);
        $mailer->isHTML(true);
        $mailer->Body = $body;
        if (!$mailer->send()) {
            \Log::error("Failed Sending Email: ".$mailer->ErrorInfo);
        }
        return $mailer->ErrorInfo;
    }    
}