<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * User Methods
 *
 * These methods are for getting and setting user data from within a
 * workflow
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2005-present humbleprogramming.com
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-User.html
 * @since      File available since Release 1.0.0
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
     * Retrieves user data from an input
     *
     * @workflow use(process) configuration(/workflow/user/information)
     * @param type $EVENT
     */
    public function information($EVENT=false) {
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cfg  = $EVENT->fetch();
            if (isset($cfg['source']) && $cfg['source']) {
                $id = false;
                if ($cfg['source'] === 'session') {
                    $id = \Environment::whoAmI();
                } else {
                    if (isset($cfg['field']) && $cfg['field']) {
                        $id = \Humble::helper('humble/event')->evaluate($data,$cfg['field']);
                    }
                }
                if ($id !== false) {
                    $EVENT->update([$cfg['node']=> \Humble::entity('default/users')->setUid($id)->information()]);
                }
            }
        }
    }
}