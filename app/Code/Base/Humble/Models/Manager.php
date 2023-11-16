<?php
namespace Code\Base\Humble\Models;
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


}
