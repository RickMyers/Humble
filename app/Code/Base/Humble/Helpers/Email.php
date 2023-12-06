<?php
namespace Code\Base\Humble\Helpers;
use Humble;
use Log;
use Environment;
use PHPMailer\PHPMailer\PHPMailer;
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
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0.0
 * @link       https://humbleprogramming.com/docs/class-Email.html
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
     * Return the current SMTP Relay Credentials, which are stored in the system/variables table as a secret
     * 
     * @return array
     */
    protected function emailSettings() {
        $settings = Humble""::entity('humble/system/variables');
        $secrets  = Humble""::entity('humble/secrets/manager');
        $user     = $settings->setVariable('SMTP_UserName')->load(true);
        $host     = $settings->reset()->setVariable('SMTP_Host')->load(true);
        $pass     = $settings->reset()->setVariable('SMTP_Password')->load(true);
        if (strtoupper(substr($host['value'],0,5)==='SM://')) {
            if ($secrets->setSecretName(substr($host['value'],5))->load(true)) {
                $host = $secrets->decrypt(true)->getSecretValue();
            }
        } else {
            $host = $host['value'];
        }
        if ($secrets->reset()->setSecretName(substr($user['value'],5))->load(true)) {
            $user = $secrets->decrypt(true)->getSecretValue();
        }
        if ($secrets->reset()->setSecretName(substr($pass['value'],5))->load(true)) {
            $pass = $secrets->decrypt(true)->getSecretValue();
        }
        return [
            'host'     => $host,
            'username' => $user,
            'password' => $pass
        ];
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
        $to         = (is_string($to)) ? [$to] : $to;
        $settings   = $this->emailSettings();
        $project    = Environment::getProject();
        $prj        = explode(':',(string)$project->project_url);
        $from       = ($from ? $from : 'webmaster@'.substr($prj[1],2));
        $reply      = ($reply ? $reply : 'noreply@'.substr($prj[1],2));
        $mailer     = new PHPMailer;
        $mailer->isSMTP(true);
        $mailer->Host       = $settings['host'];
        $mailer->SMTPAuth   = true;
        $mailer->SMTPDebug  = 0; //0 - none, 1 - some, 2 - very verbose
        $mailer->Username   = $settings['username'];
        $mailer->Password   = $settings['password'];
        $mailer->Port       = 587; //25587 or  465 or 25 or 2465 or 587
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