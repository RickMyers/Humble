<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Admin User Actions
 *
 * Methods specifically for performing administrator actions
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <>
 */
class User extends Model
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
     * Performs a standard single source authentication
     *
     * @return boolean
     */
    public function login($resource='admin/users') {
        $login      = false;
        $password   = $this->getPassword();
        $user_name  = $this->getUserName();
        $user       = Humble::entity($resource)->setUserName($user_name)->load(true);
        if ($user && ($login = ($user['password'] === crypt($password,$user['salt'])))) {
            Environment::session('user',$user);
            Environment::session('admin_id',$user['id']);
        }
        return $login;
    }
    
    /**
     * Removes the admin id token from the session, thus requiring the user to log back in as an admin
     * 
     * @return bool
     */
    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            unset($_SESSION['admin_id']);
        }
        return true;
    }
}