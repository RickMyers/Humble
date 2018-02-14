<?php
namespace Code\Base\Workflow\Models;
use Argus;
use Log;
use Environment;
/**    
 *
 * Workflow Decision Components
 *
 * General decisions for us in workflows
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers <rick@enicity.com>
 * @copyright  2005-present Enicity.com
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Decision.html
 * @since      File available since Release 1.0.0
 */
class Decision extends Model
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
     * Consults the global work flow return code to see if it has a value and has a specific value of true
     * 
     * @workflow use(decision)
     * @global boolean $workflowRC
     * @return boolean
     */
    public function success($EVENT=false) {
        global $workflowRC;
        
        return ($workflowRC === true);
    }
    
    /**
     * Consults the global workflow return code to see if it has a value and has a specific value of false
     * 
     * @workflow use(decision)
     * @global boolean $workflowRC
     * @return boolean
     */    
    public function failed($EVENT=false) {
        global $workflowRC;
        
        return ($workflowRC === false);        
    }

    /**
     * Checks to see if the workflow that just completed has turned off bubbling, usually as a result of a serious error
     * 
     * @workflow use(decision)
     * @global boolean $cancelBubble
     * @param type $EVENT
     * @return boolean
     */
    public function canceled($EVENT=false) {
        global $cancelBubble;
        
        return ($cancelBubbel === true);
    }
    
    /**
     * Checks to see if the workflow that just completed still allows "bubbling" of events
     * 
     * @workflow use(decision)
     * @global boolean $cancelBubble
     * @param type $EVENT
     * @return boolean
     */    
    public function bubbling($EVENT=false) {
        global $cancelBubble;
        
        return ($cancelBubbel === false);
    }   
    
    /**
     * Returns true if both the global workflow status variables have been set to worst case
     * 
     * @workflow use(decision)
     * @global boolean $cancelBubble
     * @param type $EVENT
     * @return boolean
     */    
    public function failedAndCanceled() {
        global $workflowRC, $cancelBubble;
        
        return (($workflowRC === false) && ($cancelBubble === true));
    }
}