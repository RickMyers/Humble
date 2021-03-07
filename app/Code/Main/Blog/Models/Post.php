<?php
namespace Code\Main\Blog\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Post related stuff
 *
 * See Title
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow Editor
 * @author     Rick Myers <rick@humblecoding.com>
 * @copyright  2005-present Humble
 * @license    https://humblecoding.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://humblecoding.com/docs/class-Post.html
 * @since      File available since Release 1.0.0
 */
class Post extends Model
{

    use \Code\Base\Humble\Event\Handler;

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
     * Pre process the post
     * 
     * @workflow emit(newPost) comment(A comment about a post) config(/blog/post/new)
     * 
     */
    public function preProcess() {
        
    }

}