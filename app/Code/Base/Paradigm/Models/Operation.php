<?php
namespace Code\Base\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * Workflow operation/program execution
 *
 * see Title
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-present, Humbleprogramming.com
 * @since      File available since Release 1.0.0
 */
class Operation extends Model
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
     * Executes an external program based on data pulled from an event
     * 
     * @workflow use(event)
     * @param event $EVENT
     */
    public function execute($EVENT=false) {
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cfg  = $EVENT->fetch();
            if (isset($cfg['program'])) {
                $str = Humble::helper('paradigm/str');
                $pgm = isset($cfg['program'])  ? $cfg['program'] : '';
                $dir = isset($cfg['directory']) ? $cfg['directory'] : '';
                $arg = isset($cfg['arguments']) ? $cfg['arguments'] : '';
                $lng = isset($cfg['language'])  ? $cfg['language'] : '';
                $fld = isset($cfg['event_field']) ? $cfg['event_field'] : false;
                $args = $str->translate($arg,$data);
                //@TODO: override segments of the arg list with values pulled from the event if %% is present
                $exec_str = $lng.' '.$dir.$pgm.' '.$args;
                $result = shell_exec($exec_str);
                if ($fld) {
                    $EVENT->update([$fld=>$result]);
                }
                $this->trigger('externalProgramExecuted',__CLASS__,__METHOD__,[
                    'working_directory' => $dir,
                    'program'     => $pgm,
                    'arguments' => $args,
                    'language' => $lng,
                    'results' => $result
                ]);
            }
        }
    }
}