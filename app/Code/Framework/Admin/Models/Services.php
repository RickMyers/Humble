<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Background Services methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Myers <rick@humbleprogramming.com>
 */
class Services extends Model
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
    public function className() {
        return __CLASS__;
    }

    /**
     * Gets the list of installed services, whether running or not
     * 
     * @return array
     */
    public function list() {
        $list     = [];
        $services = shell_exec('service --status-all');
        foreach (explode("\n",$services) as $idx => $service) {
            if ($service) {
                $list[] = [
                    'running' => substr($service,3,1)==='+',
                    'service' => substr($service,8)
                ];
            }
        }
        return $list;
    }
}