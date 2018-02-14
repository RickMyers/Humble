<?php
namespace Code\Base\Workflow\Models;
use Humble;
/**
 *
 * Manages Report Related Workflow Actions
 *
 * This class integrates with Reportico to provide basic to intermediate
 * degrees of report interaction
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Rick Myers <rick@enicity.com>
 * @since      File available since Release 1.0.0
 */
class ReportManager extends Model
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
     * Will allow a person to attach a report to the event
     *
     * @param type $EVENT
     * @workflow use(process,report) configuration(config/report/add)
     */
    public function addReport($EVENT=false) {

    }

    /**
     * A project is determined as being a directory in the 'reports/projects' folder that starts with an upper case letter.  The lower case entries are "junk"
     *
     * @return array
     */
    public function fetchProjects() {
        $dir      = 'reports/projects';
        $util     = Humble::getHelper('core/directory');
        $projects = [];
        foreach ($util->listDirectory($dir) as $entry) {
            if (ctype_upper(substr($entry,0,1))) {
                $projects[] = $entry;
            }
        }
        return $projects;


    }
}