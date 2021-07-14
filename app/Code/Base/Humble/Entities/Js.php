<?php
namespace Code\Base\Humble\Entities;
use Humble;
use Log;
use Environment;
/**
 *
 * 
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rick@humbleprogramming.com.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Release 1.0.0
 */
class Js extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function fetchEnabled($package) {
        $query = <<<SQL
           select a.*, b.enabled
             from humble_js as a
             left outer join humble_modules as b
               on a.namespace = b.namespace
          where b.enabled = 'Y'
              and a.package = '{$package}'
SQL;
        return $this->query($query);
    }
    
}
