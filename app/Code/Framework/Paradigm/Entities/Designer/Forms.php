<?php
namespace Code\Framework\Paradigm\Entities\Designer;
use Humble;
use Log;
use Environment;
/**
 *
 * Designer Forms methods
 *
 * see title
 *
 * PHP version 7.2+
 *
 * @category   Entity
 * @package    Core
 * @author       rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Forms.html
 * @since      File available since Release 1.0.0
 */
class Forms extends \Code\Framework\Paradigm\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function loadForm() {
        $id = $this->getId();
        $query = <<<SQL
            select name,id,image_name,modified
              from paradigm_designer_forms
              where id = '{$id}'

SQL;
        return $this->query($query);
    }

}