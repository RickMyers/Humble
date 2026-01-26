<?php
namespace Code\Framework\Paradigm\Entities\Workflow;
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Paradigm
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0
 * @since      File available since Version 1.0.1
 */
class Components extends \Code\Framework\Paradigm\Entities\Entity
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Does the equivalent of a distinct on the returned components
     *
     * @return array
     */
    public function components() {
        $data   = parent::fetch();
        $cps    = [];
        $comps  = [];
        foreach ($data as $idx => $row) {
            if (!isset($cps[$row['component']])) {
                $cps[$row['component']] = true;
                $comps[] = $row;
            }
        }
        return $comps;
    }

 }
?>