<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Installation Class
 *
 * Methods used during the installation process
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Core
 * @author       <rick@humbleprogramming.com>
 * @copyright  2007-present, Humbleprogramming.com
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-OnInstall.html
 * @since      File available since Release 1.0.0
 */
class OnInstall extends Model
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

    public function execute() {
        
    }
}