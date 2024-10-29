<?php
namespace Code\Framework\Humble\Entities;
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
     * Creates a new user properly salting and encoding the password
     * 
     * @param type $user_name
     * @param type $md5_password
     * @param type $last_name
     * @param type $first_name
     * @return type
     */
    public function newUser($user_name='',$md5_password='',$last_name='',$first_name='',$email='',$gender='',$dob='',$id='') {
        $uname  = $user_name ? $user_name       : ($this->getUserName()  ? $this->getUserName()  : false);
        $pwd    = $md5_password ? $md5_password : ($this->getPassword()  ? $this->getPassword()  : false);
        $fname  = $first_name ? $first_name     : ($this->getFirstName() ? $this->getFirstName() : '');
        $lname  = $last_name ? $last_name       : ($this->getLastName()  ? $this->getLastName()  : '');
        $id     = $id ? $id                     : ($this->getId()        ? $this->getId()        : '');
        $email  = $email ? $email               : ($this->getEmail()     ? $this->getEmail()     : '');
        $gender = $gender ? $gender             : ($this->getGender()    ? $this->getGender()    : '');
        $dob    = $dob ? $dob                   : ($this->getDateOfBirth() ? $this->getDateOfBirth() : '');
        $dob    = date('Y-m-d',strtotime($dob));
        if ($uname && $pwd) {
            if ($id) {
                $this->setId($id);
            }
            if ($id = $this->setSalt($this->salt())->setPassword(crypt($pwd,$this->getSalt()))->setUserName($uname)->save()) {
                Humble::entity('default/user/identification')->setId($id)->setEmail($email ?? '')->setFirstName($fname ?? '')->setLastName($lname ?? '')->setDateOfBirth($dob??'')->setGender($gender??'')->save();
            } else {
                \Log::error('Failed attempting to create user '.$uname);
            }
        }
        return $id;
    }
    
    /**
     * Will take a user id (uid) and a password already in MD5 format and update the users password
     * 
     * @param int $user_id
     * @param string $md5_password
     */
    public function updatePassword($uid=false,$md5_password=false) {
        $uid    = $uid ? $uid                   : ($this->getUid()  ? $this->getUid()  : '');
        $pwd    = $md5_password ? $md5_password : ($this->getPassword()  ? $this->getPassword()  : false);
        if ($uid && $pwd) {
            $this->setId($uid)->setSalt($this->salt())->setPassword(crypt($pwd,$this->getSalt()))->save();
        }
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
        $id      = ($id!==false) ? $id : (($this->getId() ? $this->getId() : (($this->getUid()) ? $this->getUid() : false)));
        $results = [];
        if ($id !== false) {
            $query = <<<SQL
                select
                    a.id,
                    a.user_name
                    , a.email
                    , a.logged_in
                    , a.account_status
                    , a.login_attempts
                    , a.authenticated
                    , a.reset_password_token
                    , b.*
                  from humble_users as a
                  left outer join humble_user_identification as b
                    on a.id = b.id
                 where a.id = '{$id}'
SQL;
                 $results = $this->query($query)->toArray();
        }
        return isset($results[0]) ? $results[0] : $results;
    }    
    
    /**
     * Returns a list of users, possibly with a last name starting with some letters
     * 
     * @return iterator
     */
    public function list() {
        $search_clause = $this->getStartsWith() ? " where last_name like '".$this->getStartsWith()."%'" : '';
        $query = <<<SQL
            select
                    a.id,
                    a.user_name
                    , a.email
                    , a.logged_in
                    , a.account_status
                    , a.login_attempts
                    , b.*
                    , c.id as admin_id
              from humble_users as a
              left outer join humble_user_identification as b
                on a.id = b.id
              left outer join admin_users as c
                on a.id = c.id
                {$search_clause}
        SQL;
        return $this->query($query);
    }
}
