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
        $vars   = clone $attributes;                                            //this has to happen or an infinite loop is created... very bizarre
        if (isset($validator->$node)) {                                         //We need to find the correct syntax scheme to compare the attribute to, since some have multiple schemes depending on parent
            foreach ($validator->$node->attributes as $idx => $schema) {
                $attr = $schema->attributes();
                if (isset($attr->parent) && ((string)$attr->parent==$parent)) {
                    break;
                }
            }
            foreach ($attributes as $attribute => $value) {
                $value = strtolower($value);
                //print($attribute."\n");
                if (!isset($schema->$attribute)) {
                    $errors[] = $attribute." is not a valid attribute of ".$node;
                    continue;
                }
                if (isset($schema->$attribute->values)) {
                    if (!isset($schema->$attribute->values->$value)) {
                        $errors[] = $value." is not a valid value for ".$attribute;
                    }
                }
                $attr = $schema->$attribute->attributes();
                if (isset($attr->conflicts)) {
                    foreach (explode(',',$attr->conflicts) as $conflict) {
                        if (isset($vars->$conflict)) {                          //Queue twilight zone music...
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
/*    private static function checkControllerNodes($parent,$nodes,$structure,$validator,$errors) {
        file_put_contents('struct.txt',print_r($nodes,true));
        foreach ($nodes as $child => $children) {
            //print($child."\n"); die();
            print_r($children); die();
            if (!isset($structure->$parent->$child)) {
                 $errors[] = 'Tag '.strtoupper($child).' is not a valid child of '.strtoupper($parent);
                 continue;
            }
            $attr = $structure->$parent->attributes();
            if (isset($attr->required)) {
                $req = $attr->required;
                foreach (explode(',',$req) as $required) {                
                    if (!isset($nodes->$required)) {
                        $errors[] = strtoupper($required).' is a required child for '.strtoupper($parent).' but was not found'."\n";
                    }
                }
            }
            
            foreach (self::tagAttributeCheck($parent,$child,$nodes->$child->attributes(),$validator) as $error) {
                $errors[] = $error;
            }
            if ($children->count()) {
                $errors = self::checkControllerNodes($child,$children,$structure,$validator,$errors);
            }
        }
        return $errors;
    }*/
    private static function checkControllerNodes($parent,$nodes,$structure,$validator,$errors) {
        //file_put_contents('struct.txt',print_r($nodes,true));

        foreach ($nodes as $index1 => $children) {
            foreach ($children as $node => $child) {
                //print("IDX: ".$index1."\n");
               // print("Parent Node: ".$node."\n");
                //file_put_contents('struct.txt',print_r($child,true)); 
                if (isset($child['children']) && count($child['children'])) {
                    foreach ($child['children'] as $index2 => $tags) {
                 //       print("Index2: ".$index2."\n");
                        foreach ($tags as $tag => $properties) {
                   //         print("Tag: ".$tag."\n");
                            if (!isset($structure->$node->$tag)) {
                                print('Tag '.strtoupper($tag).' is not a valid child of '.strtoupper($node)." on line number ".$properties['lineNumber']."\n");
                                 $errors[] = 'Tag '.strtoupper($tag).' is not a valid child of '.strtoupper($node)." on line number ".$properties['lineNumber'];
                                 continue;
                            }
                        }
                    }
                    $errors = self::checkControllerNodes($child,$child['children'],$structure,$validator,$errors);

                } 
            }
        
/*
            $attr = $structure->$parent->attributes();
            if (isset($attr->required)) {
                $req = $attr->required;
                foreach (explode(',',$req) as $required) {                
                    if (!isset($nodes->$required)) {
                        $errors[] = strtoupper($required).' is a required child for '.strtoupper($parent).' but was not found'."\n";
                    }
                }
            }
            
            foreach (self::tagAttributeCheck($parent,$child,$nodes->$child->attributes(),$validator) as $error) {
                $errors[] = $error;
            }
 * 
 */
        }
        return $errors;
    }


    private static function hashAttributes($attributes=false) {
        $attrs = [];
        foreach ($attributes as $attribute) {
            $attrs[$attribute->nodeName] = $attribute->nodeValue;
        }
        return $attrs;
    }
    
    private static function recurseControllerNodes($dom=[]) {
        $struct = [];
        if ($dom->hasChildNodes()) {
            foreach ($dom->childNodes as $tag => $node) {
                if (isset($node->tagName)) {
                    $tagName = $node->tagName;
                    $t = [
                        $tagName => [
                            "lineNumber" => $node->getLineNo(),
                            "attributes" => [],
                            "children"   => []
                        ]
                    ];
                    $t[$tagName]['attributes']  = ($node->hasAttributes()) ? self::hashAttributes($node->attributes) : [];
                    $t[$tagName]['children']    = ($node->hasChildNodes()) ? self::recurseControllerNodes($node): [];
                    $struct[] = $t;
                }

            }
        }
        return $struct;
    }    
    
    /**
     * Will validate a controller against a structural XML specification and an attribute value XML specification
     * 
     * @return array
     */
    public static function syntaxCheck() {
        $args       = self::arguments();
        $errors     = [];
        if ($module = \Humble::module($args['ns'])) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['controllers'].'/'.str_replace('.xml','',$args['cn']).'.xml')) {
                $dom        = new DOMDocument();
                $xml        = $dom->loadXML(file_get_contents($file));
                $struct     = self::recurseControllerNodes($dom->firstChild);
                $source     = simplexml_load_file($file);
                //print_r($source);die();
                $structure  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Structure.xml');
                $validator  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Attributes.xml');
                $errors     = self::checkControllerNodes('controller',$struct,$structure,$validator,$errors);
            } else {
                $errors[] = "Controller not found";
            }
        } else {
            $errors[] = "Module not found";
        }
        return $errors;
    }
}


