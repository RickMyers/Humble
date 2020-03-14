<?php
namespace Code\Base\Paradigm\Entities\Workflow;
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://license.enicity.com
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Version 1.0.1
 */
class Components extends \Code\Base\Paradigm\Entities\Entity
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