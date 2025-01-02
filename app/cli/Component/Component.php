<?php
require_once 'cli/CLI.php';
class Component extends CLI 
{
 
    /**
     * Compiles a controller 
     */
    public static function compile() {
        $args       = self::arguments();
        $file       = $args['file'];
        print($file."\n");
        $compiler   = \Environment::getCompiler();
        $compiler->compileFile($file);
    }

    /**
     * Builds a controller
     */
    public static function build() {
        $util = Humble::model('admin/utility',true);
        foreach (self::arguments() as $field => $value) {
            $method  = 'set'.self::underscoreToCamelCase($field,true);
            $util->$method($value);
        }
        $use_landing = $util->getLanding() ? true : false;
        $util->createController($use_landing,true);
    }
    
    /**
     * Builds a component, such as Model, Entity, or Helper
     */
    public static function create() {
        $args       = self::arguments();
    }
    
    /**
     * Checks a passed in attribute against a particular syntax scheme comparing structure and attribute values allowed
     * 
     * @param type $parent
     * @param type $node
     * @param type $attributes
     * @param type $validator
     * @return string
     */
    private static function tagAttributeCheck($parent,$node,$attributes,$validator) {
        $errors = [];
        if (isset($validator->$node)) {                                         //We need to find the correct syntax scheme to compare the attribute to, since some have multiple schemes depending on parent
            foreach ($validator->$node->attributes as $idx => $schema) {
                $attr = $schema->attributes();
                if (isset($attr->parent) && ((string)$attr->parent==$parent)) {
                    break;
                }
            }
            foreach ($attributes as $attribute => $value) {
                if (!isset($schema->$attribute)) {
                    $errors[] = $attribute." is not a valid attribute of ".$node;
                    continue;
                }
                if (isset($schema->$attribute->values)) {
                    if (!isset($schema->$attribute->values->$value)) {
                        $errors[] = $value." is not a valid value for ".$attribute;
                    }
                }
                if (isset($schema->$attribute->conflicts)) {
                    foreach (explode(',',$schema->$attribute->conflicts) as $conflict) {
                        if (isset($attribute->$conflict)) {
                            $errors[] = 'Conflict detected, '.$attribute.' and '.$conflict.' are mutually exclusive, choose one'; 
                        }
                    }
                }
            }
        } else {
            $errors[] = "No validation scheme found for ".$node;
        }
        return $errors;
    }
    
    /**
     * Checks a particular parent/child combination against the Structure tree to see if that is a valid combination
     * 
     * @param type $parent
     * @param type $nodes
     * @param type $structure
     * @param type $attributes
     * @param type $depth
     * @return string
     */
    private static function checkControllerNodes($parent,$nodes,$structure,$validator,$errors) {
        foreach ($nodes as $child => $children) {
            if (!isset($structure->$parent->$child)) {
                 $errors[] = 'Tag '.$child.' is not a valid child of '.$parent;
                 continue;
            }
            foreach (self::tagAttributeCheck($parent,$child,$nodes->$child->attributes(),$validator) as $error) {
                $errors[] = $error;
            }
            if ($children->count()) {
                $errors = self::checkControllerNodes($child,$children,$structure,$validator,$errors);
            }
        }
        return $errors;
    }

    /**
     * Will validate a controller against a structural XML specification and an attribute value XML specification
     * 
     * @return array
     */
    public static function syntaxCheck() {
        $args     = self::arguments();
        $errors   = [];
        if ($module = \Humble::module($args['ns'])) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['controllers'].'/'.str_replace('.xml','',$args['cn']).'.xml')) {
                $source     = simplexml_load_file($file);
                $structure  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Structure.xml');
                $validator  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Attributes.xml');
                foreach ($errors = self::checkControllerNodes('controller',$source,$structure,$validator,$errors) as $idx => $message) {
                    print($idx+1 .') '.$message."\n");
                }
            } else {
                die('Controller not found'."\n\n");
            }
        } else {
            die("\nModule not found\n\n");
        }
        return $errors;
    }
}


