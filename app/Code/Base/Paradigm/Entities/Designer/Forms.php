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
 * @author       rick@enicity.com
 * @copyright  2005-Present Humble Project
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Forms.html
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