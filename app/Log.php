<?php
/**
 * Manages logging activities
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Original Author <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0.1
 * @since      File available since Version 1.0.1
 */
class Log {

    private static $project = false;

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Necessary for basic level of debugging except for entities.  Do not over ride this method if you are in an entity
     *
     * @return system
     */
    public static function getClassName() {
        return __CLASS__;
    }

    /**
     * Sends data to the Firefox/Chrome console
     *
     * @param mixed $message
     * @return mixed
     */
    public static function console($message) {
        Singleton::getFirePHP()->log($message);
        return $message;
    }

    /**
     *
     */
    public static function getConsole() {
        return Singleton::getFirePHP();
    }

    /**
     * Puts a message a the TOP of the specified logs
     *
     * @param string $message
     * @param string $file
     */
    private static function prependFile($message,$file) {
        if (!file_exists($file)) {
            file_put_contents($file,'');  /* create it if it doesn't exist */
        }
        if (!is_string($message)) {
            $message = print_r($message,true);
        } else {
            $message .= "\n";
        }
        $handle     = fopen($file, "r+");
        if (!is_resource($handle)) {
            \Log::console('LOG: could not allocate a resource ['.$file.'] for message: '.$message);
            \Log::console(debug_backtrace());
            return;
        }
        $len        = strlen($message);
        $final_len  = filesize($file) + $len;
        $original   = fread($handle, $len);
        rewind($handle);
        $i = 1;
        while (ftell($handle) < $final_len) {
            fwrite($handle, $message);
            $message = $original;
            $original = fread($handle, $len);
            fseek($handle, $i * $len);
            $i++;
        }
    }

    public static function getProject() {
        if (!self::$project) {
            self::$project = \Environment::getProject();
        }
        return self::$project;
    }

    /**
     * Sends data to the general message log
     *
     * @param mixed $message
     */
    public static function general($message) {
        $project = self::getProject();
        $file    = '../../logs/'.$project->namespace.'/general.log';
        if ($message) {
            self::prependFile($message, $file);
        }
    }

    /**
     * Sends data to the users personal message log
     *
     * @param mixed $message
     */
    public static function user($message) {
        if (isset($_SESSION['login'])) {
            $project = self::getProject();
            $file    = '../../logs/'.$project->namespace.'/'.$_SESSION['login'] .'.log';
            if ($message) {
                self::prependFile($message, $file);
            }
        }
    }

    /**
     * Sends data to the warning log
     *
     * @param mixed $message
     */
    public static function warning($message) {
        $project = self::getProject();
        $file    = '../../logs/'.$project->namespace.'/warning.log';
        if ($message) {
            self::prependFile($message, $file);
        }
    }

    /**
     * Send data to the error log
     *
     * @param mixed $message
     */
    public static function error($message) {
        $project = self::getProject();
        $file    = '../../logs/'.$project->namespace.'/error.log';
        if ($message) {
            self::prependFile($message, $file);
        }
    }

    /**
     * Send data to the mysql error log
     *
     * @param mixed $message
     */
    public static function mysql($message) {
        $project = self::getProject();
        $file    = '../../logs/'.$project->namespace.'/mysql.log';
        if (is_array($message)) {
            $message = implode("\n",$message);
        }
        self::prependFile($message, $file);
    }

    /**
     *
     */
    public static function signal($subject,$messages) {
        if (is_array($messages)) {
            $messages = implode("\n",$messages);
        }
        $text = 'Attention, the following alerts have been issued:<br/><br/><br/>';
        $text .= "<pre>\n".$messages."\n</pre><br />";
        $text .= "Additional information follows:<br /><br />";
        $arr = array();
        foreach ($_SESSION as $name => $val) {
            $arr[] = $name.' = '.$val;
        }
        $text .= "<b>SESSION INFORMATION</b><br /><pre>\n".implode("\n",$arr)."\n</pre><br />";
        $arr = array();
        foreach ($_GET as $name => $val) {
            $arr[] = $name.' = '.$val;
        }
        $text .= "<b>HTTP GET</b><br /><pre>\n".implode("\n",$arr)."\n</pre><br />";
        $arr = array();
        foreach ($_POST as $name => $val) {
            $arr[] = $name.' = '.$val;
        }
        $text .= "<b>HTTP POST</b><br /><pre>\n".implode("\n",$arr)."\n</pre><br />";
        $headers = 'From: alert@humbleprogramming.com' ."\r\n" .
                   'Reply-To: noreply@humbleprogramming.com' . "\r\n" .
                   'Content-Type: text/html' . "\r\n" .
                   'X-Mailer: PHP/' .phpversion();
        mail('rick@humbleprogramming.com',$subject,$text,$headers);
    }

    /**
     * Logs an activity performed by someone, possibly on behalf of someone, and intended to be displayed on the dashboard
     *
     * @param type $primary         The primary target of the action
     * @param type $secondary       The person who is either performing an action for someone, or on behalf of someone
     * @param type $template        The template for the activity message
     * @param type $data            An array of values to use to construct the message
     */
    public static function activity($primary=null,$secondary=null,$name=null,$data=[]) {
        $template = Humble::getEntity('humble/activity_templates')->setName($name)->load(true);
        if ($template) {
            $actors     = ['primary_id'=>null,'secondary_id'=>null];
            if ($template['meta_data']) {
                foreach (explode(',',$template['meta_data']) as $meta_data) {
                    $m  = explode('=',$meta_data);
                    $actors[$m[0]] = isset($data[$m[1]]) ? $data[$m[1]] : null;
                }
            }
            $primary_id     = (isset($actors['primary_id']) && $actors['primary_id']) ? $actors['primary_id'] : $primary;
            $secondary_id   = (isset($actors['secondary_id']) && $actors['secondary_id']) ? $actors['secondary_id'] : $secondary;
            Humble::getEntity('humble/activity_log')->setLoggedBy(Environment::whoAmI())->setPrimaryId($primary_id)->setSecondaryId($secondary_id)->setActivity(Humble::getHelper('humble/string')->translate($template['template'],$data))->save();
        }
    }

    /**
     *
     */
    public function __clone()        {        }
    public function __wakeup()       {        }
}
