<?php
namespace Code\Base\Paradigm\Entities;
/**
 *
 * Paradigm Workflows
 *
 * This entity class contains queries that are used when building
 * workflows in the Editor.
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Workflow
 * @author     Rick Myers rick@humblecoding.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://humblecoding.com/license.txt
 * @version    1.0
 * @link       https://humblecoding.com/docs/class-Paradigm_Entities_Workflows.html
 * @since      File available since Version 1.0.1
 */
class Workflows extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Lists workflow data along with the user name of the person who created it and respects pagination
     *
     * @return array
     */
    public function inventory() {
        $query = <<<SQL
        select a.*, b.first_name, b.last_name
          from paradigm_workflows as a
         inner join humble_user_identification as b
             on a.creator = b.id
         where namespace = '{$this->getNamespace()}'
SQL;
        return $this->query($query);
    }

    /**
     * Generates a list of current workflows will a full set of associated information, including how they are triggered, the creator, and the URI if it is being triggered from a webservice
     *
     * @return type
     */
    public function fullList() {
        $query = <<<SQL
            SELECT  a.id, a.workflow_id, a.major_version, a.minor_version, a.title, a.generated, a.generated_workflow_id, a.namespace, a.active,
                    b.namespace AS listener_namespace, b.component AS listener_component, b.method AS listener_method, b.active AS listener_active,
                    c.uri AS webservice_uri, c.active AS webservice_active,
                    d.`first_name`, d.`last_name`
              FROM  paradigm_workflows AS a
              LEFT  OUTER JOIN paradigm_workflow_listeners AS b
                ON  a.workflow_id = b.`workflow_id`
              LEFT  OUTER JOIN paradigm_webservice_workflows AS c
                ON  a.workflow_id = c.`workflow_id`
              LEFT  OUTER JOIN humble_user_identification AS d
                ON  a.creator = d.id
SQL;
        return $this->query($query);
    }

}