<?php
namespace Code\Base\Humble\Entities;
use Humble;
use Log;
use Environment;
/**
 *
 * Core User table related methods
 *
 * see title...
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rick@humblecoding.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Users extends Entity
{

    private $alphabet = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Creates a new user properly salting and encoding the password
     * 
     * @param type $user_name
     * @param type $md5_password
     * @param type $last_name
     * @param type $first_name
     * @return type
     */
    public function newUser($user_name='',$md5_password='',$last_name='',$first_name='',$uid='') {
        $uname  = $user_name ? $user_name       : ($this->getUserName()  ? $this->getUserName()  : false);
        $pwd    = $md5_password ? $md5_password : ($this->getPassword()  ? $this->getPassword()  : false);
        $fname  = $first_name ? $first_name     : ($this->getFirstName() ? $this->getFirstName() : '');
        $lname  = $last_name ? $last_name       : ($this->getLastName()  ? $this->getLastName()  : '');
        $uid    = $uid ? $uid                   : ($this->getUid()  ? $this->getUid()  : '');
        if ($uname && $pwd) {
            if ($uid) {
                $this->setUid($uid);
            }
            if ($id = $this->setSalt($this->salt())->setPassword(crypt($pwd,$this->getSalt()))->setUserName($uname)->save()) {
                if ($fname && $lname) {
                    Humble::getEntity('humble/user/identification')->setId($id)->setFirstName($fname)->setLastName($lname)->save();
                }
            }
        }
        return $id;
    }
    
    /**
     * Creates a salt token
     * 
     * @param type $length
     * @return type
     */
    public function salt($length=16) {
        $salt = ''; $len = strlen($this->alphabet);
        for ($i=0; $i<$length; $i++) {
            $salt .= substr($this->alphabet,rand(0,$len),1);
        }
        return $salt;
    }
    
    /**
     * Assuming you passed in a token value, did it find it in the table?
     *
     * @return boolean
     */
    public function newPasswordRequestValid() {
        return ($this->load(true)!==null);
    }

    /**
     * Returns true if we are able to load a user record by password reset token
     *
     * @return boolean
     */
    public function resetTokenIsValid() {
        return ($this->load(true)!==null);
    }

    /**
     * This clears the users account so that they may log in again, assuming the proper token was passed
     */
    public function resetLoginAttempts() {
        $data = $this->load(true);
        if ($data) {
            $this->setLoginAttempts(0);
            $this->setAccountStatus(USER_ACCOUNT_UNLOCKED);
            $this->setResetPasswordToken(null);
            $this->save();
        }
    }
    /**
     *
     * @param int $id
     * @return array
     */
    public function information($id=false) {
        $id      = ($id!==false) ? $id : ($this->getId() ? $this->getId() : ($this->getUid())?$this->getUid() : false);
        $results = [];
        if ($id !== false) {
            $query = <<<SQL
                select
                    a.user_name
                    , a.email
                    , a.logged_in
                    , a.account_status
                    , a.login_attempts
                    , b.*
                  from humble_users as a
                  left outer join humble_user_identification as b
                    on a.uid = b.id
                 where a.uid = '{$id}'
SQL;
                 $results = $this->query($query)->toArray();
        }
        return isset($results[0]) ? $results[0] : $results;
    }


}