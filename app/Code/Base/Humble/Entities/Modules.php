<?php
namespace Code\Base\Humble\Entities;
use Humble;
/**    
 *
 * Core Modules queries and related methods
 *
 * See Title
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Modules extends Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     *
     *
     * @return iterator
     */
    public function fetchNonBase() {
        $query = <<<SQL
                select * from humble_modules where package != 'Base'
SQL;
        return $this->query($query);
    }
}