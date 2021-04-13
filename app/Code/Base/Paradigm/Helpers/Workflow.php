<?php
/**
 *
 * Workflow helper utilities
 *
 * This helper contains methods that will be used by components in
 * workflows, be it loading workflow component configurations, saving,
 * etc...
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Workflow
 * @author     Rick Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
  * @since      File available since Version 1.0.1
 */
class Workflow extends Helper
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
     * returns the configuration information for a component from mongo
     *
     * @param string $id [optional]
     * @return mixed
     */
    public function load($id=false) {
        $id = ($id) ? $id : $this->getId();
        $mongo = \Humble::getCollection('paradigm/elements');
        $mongo->setId($id);
        ($results = $mongo->load()) ? $results : array();
        return json_encode($results);
    }
}