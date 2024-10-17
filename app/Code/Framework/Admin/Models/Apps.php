<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Admin Apps Administration
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Apps extends Model
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
     * Gets all the available administration apps that will be represented in the admin desktop area
     * 
     * @return array
     */
    public function list() {
        $admin_apps = [];
        foreach (Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            if (file_exists($apps_file = 'Code/'.$module['package'].'/'.$module['module'].'/etc/AdminApps.xml')) {
                foreach ($apps = simplexml_load_file($apps_file) as $app) {
                    $admin_apps[] = [
                        'name' => (string)$app->name,
                        'description' => (string)$app->description,
                        'title' => (string)$app->icon->text,
                        'image' => (string)$app->icon->image->desktop,
                        'minimized_icon' => (string)$app->icon->image->minimized,
                        'uri' => (string)$app->start->uri
                    ];
                }
            }
        }
        return $admin_apps;
    }
}