<?php
namespace Code\Base\Humble\Entities;
use Humble;
/**    
 *
 * Modules queries and related methods
 *
 * See Title
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Framework
 * @author     Richard Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
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