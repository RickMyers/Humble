<?php
namespace Code\Main\Account\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Project Registration
 *
 * Methods used when registering projects
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Registration extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;

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
     * Creates a 4 part, 16 character (19 total including dashes) serial number
     * 
     * @return string
     */
    public function serialNumber() {
        $chars   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num     = '';
        for ($i=0; $i<4; $i++) {
            $num = $num ? $num.'-' : $num;
            for ($j=0; $j<4; $j++) {
                $num.=substr($chars,rand(0,strlen($chars)-1),1);
            }
        }        
        return $num;
    }
    
    /**
     * Registers a project by assigning a serial number, if that project already exists, it returns the existing serial number
     * 
     * @workflow use(EVENT)
     * @param type $details
     * @return string
     */
    public function registerNew($details) {
        $serial_number  = false;
        $project_name   = $details['project_name'];
        $email          = $details['author'] ?? $details['email'];
        $URL            = $details['project_url'];
        $factory        = $details['factory_name'];
        $orm            = Humble::entity('account/registrations');
        if ($data = $orm->setEmail($email)->setProject($project_name)->load(true)) {
            $serial_number = $data['serial_number'] ?? false;
        } 
        if (!$serial_number) {
            $not_found = true; $ctr = 0;
            while ($not_found && (++$ctr<10)) {                                 //we are going to try a max of 9 times to come up with a unique 16 digit serial number
                if (!$not_found  = $orm->reset()->setSerialNumber($serial_number)->load(true)) {
                    $serial_number = $this->serialNumber();
                }
            }
            if ($serial_number) {
                $orm->reset()->setSerialNumber($serial_number)->setEmail($email)->setProject($project_name)->setProjectUrl($URL)->setFactory($factory)->setProjectDetails($this->getProjectDetails())->save();
            }
        }
        return $serial_number;
    }
    
    /**
     * Takes an existing .project file and updates our registry
     * 
     * @param type $details
     */
    public function registerExisting() {
        $details        = json_decode($this->getProjectDetails(),true);
        $result         = 'Registration Failed';
        $project_name   = $details['project_name'];
        $serial_number  = $details['serial_number'] ?? false;
        $factory        = $details['factory_name']  ?? false;
        $email          = $details['author']        ?? false;
        $URL            = $details['project_url']   ?? false;
        if ($serial_number && $factory && $email && $URL) {
            Humble::entity('account/registrations')->setSerialNumber($serial_number)->setEmail($email)->setProject($project_name)->setProjectUrl($URL)->setFactoryName($factory)->setProjectDetails($this->getProjectDetails())->save();
            $result = 'Registration Successful';
        }
        return $result;
    }
    
    /**
     * Returns just the json that was captured during registration
     * 
     * @param type $serial_number
     * @return type
     */
    public function install($serial_number) {
        $details = [];
        if ($data = Humble::entity('account/registrations')->setSerialNumber($serial_number)->load(true)) {
            $details = $data['project_details'] ?? '';
        }
        return $details;
    }
}