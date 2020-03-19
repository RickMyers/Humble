<?php
namespace Code\Base\Paradigm\Entities\Designer;
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
 * @author       rick@humblecoding.com
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://humblecoding.com/license.txt
 * @version    1.0
 * @link       https://humblecoding.com/docs/class-Forms.html
 * @since      File available since Release 1.0.0
 */
class Forms extends \Code\Base\Paradigm\Entities\Entity
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