<?php
/**
 * Exception Handler
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
class HumbleException {
    //put your code here

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
     * Generates the output when an exception is encountered
     *
     * @param Exception $e
     * @param type $type
     * @param type $template
     */
    public static function standard($e=false,$type="An Error Has Occurred",$template='standard') {
        if ($e && ($e instanceof Exception )) {
            header("HTTP/1.1 400 Bad Request");
            $ts = date('Y-m-d H:i:s');
            $ns = Humble::_namespace();
            $cn = Humble::_controller();
            $ac = Humble::_action();
            $rq = print_r($_REQUEST,true);
            $filename = '';
            if (\Environment::getApplication('exceptions')=='JSON') {
                header('Content-Type: application/json');
                $message = strip_tags($e->getMessage());
                $JSON = <<<JSON
                {
                    "message": "{$message}",
                    "rc":      "{$e->getCode()}"
                }   
JSON;
                print($JSON);
            } else {
                $rain = \Environment::getInternalTemplater('Code/Framework/Humble/Views/Exceptions/');
                $rain->assign('ex',$e);
                $rain->assign('title',$type);
                $rain->assign('dump',htmlentities($e->getTraceAsString()));
                $rain->assign('filename',($filename  = (method_exists($e,'getFileName')) ? $e->getFileName() : "N/A"));
                $rain->draw($template);
            }
            $exception = <<<ERRORTEXT
--------------------------------------------------------------------------------
{$ts} - {$type}
RETURN CODE:   {$e->getCode()}
ERROR MESSAGE: {$e->getMessage()}
ERROR FILE:    {$e->getFile()}
SOURCE FILE:   {$filename}
ACTION:        /{$ns}/{$cn}/{$ac}
STACK TRACE:

{$e->getTraceAsString()}

REQUEST:
{$rq}
ERRORTEXT;
            \Log::error($exception);
        }
    }

    /**
     *
     * @param type $ex
     * @param type $type
     * @param type $template
     */
    public static function mongo($ex,$type="An Error Has Occurred",$template='mongo') {
            $stamp   = date('Y-m-d H:i:s');
            $message = <<<MSG
<error stamp="{$stamp}">
    <message>{$type}</message>
    <code>{$ex->getError()}</code>
    <reason>{$ex->getCode()}</reason>
</error>
MSG;
        $file = Humble::helper('humble/file');
        if ($file->set('../../logs/humble/mongo.log')) {
            $file->prepend($message);
        }
    }

    /**
     *
     */
    public function __clone()        {        }
    public function __wakeup()       {        }
}
