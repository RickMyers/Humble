<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Administration Menus
 *
 * Methods for manipulating the administration menus
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author       <>
 */
class Menus extends Model
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
     * Groups the serial menu data from the database into a hierarchical tree
     * 
     * @return array
     */
    public function sort() : array {
        $categories = [];
        $pointers   = [];
        $menus      = [];
        foreach (Humble::entity('admin/menu/categories')->orderBy('seq=ASC')->fetch() as $category) {
            $categories[$category['id']] = $category['category'];
        }
        $options = Humble::entity('admin/menus')->orderBy('category_id=ASC,parent_id=ASC,seq=ASC')->fetch();
        foreach ($options as $menu) {
            $menu['children'] = [];
            if ($menu['parent_id']=='') {
                $menus[$categories[$menu['category_id']]][$menu['id']] = $menu;
                $pointers[$menu['id']] = &$menus[$categories[$menu['category_id']]][$menu['id']];
            }
            
        }
        foreach ($options as $menu) {
            //$menu['children'] = [];
            if ($menu['parent_id']) {
                $pointers[$menu['parent_id']]['children'][$menu['id']] = $menu;
            } 
        }        
        return $menus;
    }
}