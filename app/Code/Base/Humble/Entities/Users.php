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
 * @author     Richard Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Users extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
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