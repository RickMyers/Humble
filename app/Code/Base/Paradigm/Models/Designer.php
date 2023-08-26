<?php
namespace Code\Base\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Form Designer Methods
 *
 * see title
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Framework
 * @author       <rick@humbleprogramming.com>
 * @copyright  2007-present, Humbleprogramming.com
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Designer.html
 * @since      File available since Release 1.0.0
 */
class Designer extends Model
{

    use \Code\Base\Humble\Traits\EventHandler;

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
     * Creates a new workflow
     *
     * @workflow use(event)
     */
    public function newForm() {
        $ok     = false;
        $id     = false;
        $i_name = '';
        $blob   = '';
        $name   = $this->getName();
        $desc   = $this->getDescription();
        $url    = $this->getUrl();
        $image  = $this->getImage();
        $form   = Humble::entity('paradigm/designer_forms');
        if ($url) {
            if ($blob = file_get_contents($url)) {
                $ok     = true;
                $p      = explode('/',$url);
                $i_name = $p[count($p)-1];
            }
        } else if ($image && isset($image['path'])) {
            if ($blob = file_get_contents($image['path']))  {
                $ok     = true;
                $i_name = $image['name'];
            }
        }
        if ($ok) {
            $id = $form->setName($name)->setDescription($desc)->setImageName($i_name)->setImage($blob)->save();
        }
        $this->trigger('designerFormCreated', __CLASS__, __METHOD__,
                [
                    'created'       => $ok,
                    'created_by'    => Environment::whoAmI(),
                    'form_id'       => $id,
                    'name'          => $name,
                    'description'   => $desc,
                    'image_name'    => $i_name
                ]);
        return json_encode(['id' =>$id,'name' => $name,'description'=>$desc]);
    }

    /**
     *
     */
    public function formBackground() {
        $form = Humble::entity('paradigm/designer_forms')->setId($this->getId())->load();
        if  ($form) {
            $p = explode('.',$form['image_name']);
            header('Content-Type: image/'.$p[count($p)-1]);
        }
        return $form['image'];
    }

    /**
     * 
     */
    public function save() {
       $form = Humble::entity('paradigm/designer_forms');
       $form->setId($this->getId())->setName($this->getName())->$this->getDescription($this->getDescription())->setFormData($this->getFormData())->setLayers($this->getLayers())->save();
    }
}