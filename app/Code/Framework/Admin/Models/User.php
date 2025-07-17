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
     * Will validate that a users password is correct
     * 
     * @return boolean
     */
    public function validatePassword($password=false) {
        $password   = ($password) ? $password : $this->getPassword();
        $user_id    = $this->getUserId();
        $user       = Humble::entity('admin/users')->setId($user_id)->load();
        return ($user['password'] === crypt($password,$user['salt']));        
    }
    
    /**
     * Changes an admins password, optionally compares against a "confirm" password
     * 
     * @return bool
     */
    public function changePassword() {
        $password    = $this->getPassword();
        $confirm     = $this->getConfirmPassword();
        if (($confirm) && ($confirm !== $password)) {
            return false;
        }
        if ($this->validatePassword($this->getCurrentPassword())) {
            if ($user_id = $this->getUserId() ? $this->getUserId() : \Environment::session('admin_id')) {
                $user    = Humble::entity('admin/users')->setId($user_id);          //getting specific ORM reference
                $data    = $user->load();                                           //Gets user data, 1 use of ORM
                return $user->setPassword(crypt($password,$data['salt']))->save();  //Changes the data, 2nd use of ORM
            }
        }
        return false;
    }
    
    /**
     * Performs a standard single source authentication
     * 
     * @workflow use(DECISION)
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
     * Returns true if you've exceeded a set number of failed login attempts
     *
     * @workflow use(decision) configuration(workflow/user/tries)
     * @param type $EVENT
     * @return boolean
     */
    public function exceededTries($EVENT=false) {
        $exceeded = true;
        if ($EVENT!==false) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user       = Humble::entity('admin/users')->setUserName($data['user_name']);
                $user->load(true);
                $mydata     = $EVENT->fetch();
                if (isset($mydata['tries']) && ($mydata['tries'])) {
                    $exceeded   = ((int)$user->getLoginAttempts() > (int)$mydata['tries']);
                }
            } else {
                //throw an exception for insufficient data
            }
        }
        return $exceeded;
    }
    
    /**
     * Allows you to set a message that will be displayed on the page after a login attempt
     *
     * @deprecated since 3.1
     * @workflow use(process) configuration(workflow/login/message)
     * @param type $EVENT
     * @return boolean
     */
    public function setLoginErrorMessage($EVENT=false) {
        if ($EVENT!==false) {
            $myId   = $EVENT->_target();
            $action = $EVENT->name;
            $data   = $EVENT->$action;
            if (isset($EVENT->configurations[$myId])) {
                $mydata     = $EVENT->configurations[$myId];
                if (isset($mydata['message'])) {
                    $EVENT->_error($mydata['message']);
                }
            } else {
                //throw an exception for insufficient data
            }
        }
        return true;
    }

    /**
     * Looks in the Humble Project file for the landing page and routes the user to it.
     *
     * @workflow use(process)
     */
    public function routeToHomePage($EVENT=false) {
        if ($EVENT!==false) {
            //just duplicating for now... not sure if I want special "polymorphic" behavior for this if passed an event
            $project = Environment::project();
            header('Location: '.$project->landing_page);
        } else {
            $project = Environment::project();
            header('Location: '.$project->landing_page);
        }
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