<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Resource Manager
 *
 * Manages resource files
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Resource extends Model
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
     * 
     * 
     * @param array $sql
     * @param array $request
     * @return string
     */
    protected function processSQLResource($sql=[],$request=[]) {
        $parsed_sql = '';
        foreach ($sql as $row) {
            $keep       = true;
            $segments   = explode('%%',$row);
            if (count($segments)===1) {
                $keep = true;
            } else {
                foreach ($segments as $idx => $segment) {
                    if ($idx % 2 !== 0) {
                        if ($keep = $keep && isset($request[$segment])) {
                            $segments[$idx] = $request;
                        }
                    }
                }
            }
            $parsed_sql .= ($keep) ? implode(' ',$segments) : '';
        }
        return $parsed_sql;
    }
    
    /**
     * Hmmmm... what to do.. what to do...
     * 
     * @param type $namespace
     * @param type $type
     * @param type $file
     * @param type $request
     */
    public function process($namespace=false,$type=false,$file=false,$with=false,$on=false,$request=[]) {
        if ($namespace && $type && $source) {
            if ($module = Humble::module($namespace)) {
                switch (strtolower($type)) {
                    case "sql"  : 
                        if (file_exists($source = 'Code/'.$module['package'].'/'.$module['module'].'/'.$module['resources_sql'].'/'.$file.'.'.$type)) {
                            $sql = $this->processSQLResource(explode("\n",file_get_contents($source)),$request);
                            
                        }
                        break;
                    case "js"   :
                        break;
                    default     :
                        break;
                }
            }
        }
    }
}