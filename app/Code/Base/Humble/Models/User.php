<?php
namespace Code\Base\Humble\Models;
use Humble;
use Environment;
/**
 *
 * Manages core user functions
 *
 * Core user functions
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Core_Model_User.html
 * @since      File available since Version 1.0.1
 */
class User extends Model {
    use \Code\Base\Humble\Event\Handler;

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
     * Returns true if you've exceeded a set number of failed login attempts
     *
     * @workflow use(decision) configuration(workflow/user/tries)
     * @param type $EVENT
     * @return boolean
     */
    public function exceededTries($EVENT=false) {
        $exceeded = true;
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user       = Humble::getEntity('humble/users')->setUserName($data['user_name']);
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
        if ($EVENT) {
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
        if ($EVENT) {
            //just duplicating for now... not sure if I want special "polymorphic" behavior for this if passed an event
            $project = Environment::getProject();
            header('Location: '.$project->landing_page);
        } else {
            $project = Environment::getProject();
            header('Location: '.$project->landing_page);
        }
    }

    /**
     * Performs a standard single source authentication
     *
     * @return boolean
     */
    protected function standardLogin() {
        $login      = false;
        $password   = $this->getPassword();
        $user_name  = $this->getUserName();
        $user       = Humble::getEntity('humble/users')->setUserName($user_name)->load(true);
        if ($user && ($login = ($user['password'] === $this->getPassword()))) {
            Environment::session('uid',$user['uid']);
            Environment::session('login',$user['uid']);
            Environment::session('user',$user);
        }
        return $login;
    }

    /**
     * SSO login functionality
     *
     * @return boolean
     */
    protected function SSOLogin() {
        return true;
    }

    /**
     * Router to switch between an SSO login and standard single source login
     *
     * @return boolean
     */
    public function login() {
        $successful = false;
        $app        = Environment::status();
        if ($app->status->SSO->enabled == 1) {
            $successful = $this->SSOLogin();
        } else {
            $successful = $this->standardLogin();
        }
        return $successful;
    }

    /**
     * Confirms that an attempt to login by using SSO succeeded
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function SSOLoginSuccessful($EVENT=false) {
        $successful = false;
        if ($EVENT) {
            $data   = $EVENT->load();
            // ‘v3-sp' is a name of the authentication provider setup in simplesamlphp, but it could be anything for any client. Based on how client want to authenticate.
            // how do we get a value in here?
            $IDP_Name = 'v3-sp';

            // Basic PHP Code Snippet:
            \SimpleSAML_Configuration::setConfigDir('vendor/simplesamlphp/simplesamlphp/config/');

            $as = new \SimpleSAML_Auth_Simple($IDP_Name);

            $as->requireAuth();
            $attributes = $as->getAttributes();

            // This could be username or uid or whatever field we configure SSO to get from an IDP
            // In fact, I can rename the field before it gets to this module, so we can keep the same variable name
//            $users->setUserName($attributes['username'][0]);

            //##################################################################
            //@TODO: We need to do a lookup on the attributes returned so that
            //       we can tie this back to a person in our system
            //##################################################################
            $user     = Humble::getEntity('humble/users');
            $user->setUserName($attributes['username'][0]);
            $user->load(true);
            if ($user->getUid()) {
                $valid = true;
            }
            $user     = $users->load(true);
            $SSO      = 'goes here';
        }
        return $successful;
    }

    /**
     * This is a redirect to another page, you may specify which page the user should be sent to
     *
     * @workflow use(process) configuration(workflow/user/redirect)
     * @param type $EVENT
     */
    public function redirect($EVENT=false) {
        if ($EVENT) {
             $mydata = $EVENT->fetch();
             if (isset($mydata['url'])) {
                 header('Location: '.$mydata['url']);
             }
        }
    }

    /**
     * This is a standard, non-SSO, login attempt
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function standardLoginSuccessful($EVENT=false) {
        $successful = false;
        if ($EVENT) {
            $data       = $EVENT->load();
            $user       = Humble::getEntity('humble/users')->setUserName($data['user_name'])->load(true);
            if (isset($user['password']) && isset($data['password']) && $data['password']) {
                if (!session_id()) {
                    session_start();
                }
                if ($successful = ($user['password'] === $data['password'])) {
                    $_SESSION['uid']    = $user['uid'];
                    $_SESSION['began']  = time();
                    $_SESSION['login']  = $user['uid']; //This is the id that was actually authenticated... it lets admins jump around posing as other users
                    $_SESSION['user']   = $user;
                }
            }
            $EVENT->update(['login'=>$successful,'password_expected'=>$user['password']]);
        }
        return $successful;
    }

    /**
     * Returns false if the login event was not successful
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function loginFailed($EVENT=false) {
        $failed = false;
        if ($EVENT) {
            $data   = $EVENT->load();
            //work this out later
        }
        return $failed;
    }

    /**
     * Resets the number of login attempts back to 0
     *
     * @param type $EVENT
     * @workflow use(process)
     */
    public function resetTries($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                if ($user->load(true)) {
                    $user->setLoginAttempts(0);
                    $user->save();
                }
            } else {
                //throw an exception for insufficient data
            }
        }
        return true;
    }

    /**
     * Adds one to the number of times you've tried to login without success
     *
     * @param type $EVENT
     * @workflow use(process)
     */
    public function incrementTries($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                if ($user->load(true)) {
                    $user->setLoginAttempts($user->getLoginAttempts()+1);
                    $user->save();
                }
            } else {
                //throw an exception for insufficient data
            }
        }
    }

    /**
     * Records the time of the last successful login
     *
     * @param type $EVENT
     * @workflow use(process)
     */
    public function recordLoginTime($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                $user->load(true);
                $user->setLoggedIn(date('Y-m-d H:i:s'));
                $user->save();
            } else {
                //throw an exception for insufficient data
            }
        }
    }

    /**
     * Sends the user the recover password process
     *
     * @workflow use(event)
     */
    public function recoverPasswordEmail() {
        $email = $this->getEmail();
        $user  = Humble::getEntity('humble/users');
        $data  = $user->setEmail($email)->load(true);
        if ($data) {
            $token = '';
            for ($i=0; $i<16; $i++) {
                $token .= rand(0,1) ? chr(rand(ord('a'),ord('z'))) : chr(rand(ord('A'),ord('Z'))) ;
            }
            $user->setResetPasswordToken($token);
            $user->save();
            $message = "\"<a href='http://humble-project/humble/user/resetform?token={$token}&email={$email}'>Click here to reset your password</a>\"";
            $this->email($email,'Reset Password Instructions',$message);
        }
        $this->trigger('recover-password-email-sent',__CLASS__,__METHOD__,array('email'=>$email,"sent"=>date('Y-m-d H:i:s')));
    }

    /**
     * Checks to see if the account is currently locked, returning true if it is
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function accountLocked($EVENT=false) {
        $locked = true;
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name'])->load(true);
                $locked = ($user['account_status']===USER_ACCOUNT_LOCKED);
            } else {
                //throw an exception for insufficient data
            }
        } else {
            //throw an trigger exception
        }
        return $locked;
    }

    /**
     * Locks the user out of the system
     *
     * @workflow use(process) authorization(true)
     * @param type $EVENT
     * @return boolean
     */
    public function lockAccount($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                $user->load(true);
                $user->setAccountStatus(USER_ACCOUNT_LOCKED);
                $user->save();
            } else {
                $EVENT->_error('Could not lock account due to lack of login id or user name');
                //throw an exception for insufficient data
            }
        } else {
            //throw an trigger exception
        }
        return true;
    }
    /**
     * Unlocks the userid
     *
     * @workflow use(process) authorization(true)
     * @param type $EVENT
     * @return boolean
     */
    public function unlockAccount($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                $user->load(true);
                $user->setStatus(USER_ACCOUNT_UNLOCKED);
                $user->save();
            } else {
                //throw an exception for insufficient data
            }
        } else {
            //throw an trigger exception
        }
        return true;
    }

    /**
     * Returns true if the password reset token is set AND it attaches the reset token to event
     *
     * @workflow use(decision)
     * @param type $EVENT
     * @return boolean
     */
    public function isResetPasswordTokenSet($EVENT) {
        $token = false;
        if ($EVENT) {
            $data = $EVENT->load();
            if (isset($data['user_name'])) {
                $user   = Humble::getEntity('humble/users')->setUserName($data['user_name']);
                $user   = $user->load(true);
                $token  = (isset($user['reset_password_token']) && $user['reset_password_token']) ? $user['reset_password_token'] : false;
                $EVENT->update(['password-reset-status'=>[
                        'token'=>$token,
                        'set' => ($token !== false)
                    ]]);
            }
        }
        return ($token !== false);
    }

    /**
     * Sets the new password value if the email and new_password_token match up
     *
     * @return boolean
     */
    public function newPassword() {
        $changed  = false;
        $password = $this->getPassword();
        $confirm  = $this->getConfirm();
        $token    = $this->getResetPasswordToken();
        $email    = $this->getEmail();
        $user     = Humble::getEntity('humble/users')->setEmail($email)->setResetPasswordToken($token);
        if ($user->load(true) && $password && ($password == $confirm)) {
            $user->setPassword($password);
            $user->setResetPasswordToken(null);
            $user->setAccountStatus(USER_ACCOUNT_UNLOCKED);
            $user->setLoginAttempts(0);
            $user->save();
            $changed = true;
        }
        return $changed;
    }

    /**
     * Logs the user out
     */
    public function logout() {
        session_destroy();
    }
    /**
     * Stores the login error or returns the last error
     *
     * @param type $msg
     * @return type
     */
    public function _error($msg=false) {
        if ($msg !== false) {
            $this->_error = $msg;
        }
        return $this->_error;
    }
}
?>