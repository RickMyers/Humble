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
    private static function tagAttributeCheck($parent,$node,$attributes,$validator,$lineNumber,$errors) {
        if (isset($validator->$node)) {                                         //We need to find the correct syntax scheme to compare the attribute to, since some have multiple schemes depending on parent
            foreach ($validator->$node->attributes as $idx => $schema) {
                $attr = $schema->attributes();
                if (isset($attr->parent) && ((string)$attr->parent==$parent)) {
                    break;
                }
            }
            foreach ($attributes as $attribute => $value) {
                $value = strtolower($value);
                if (!isset($schema->$attribute)) {
                    $errors[] = $attribute." is not a valid attribute of ".$node." on line number ".$lineNumber;
                    continue;
                }
                if (isset($schema->$attribute->values)) {
                    if (!isset($schema->$attribute->values->$value)) {
                        $errors[] = $value." is not a valid value for ".$attribute." on line number ".$lineNumber;
                    }
                }
                $attr = $schema->$attribute->attributes();
                if (isset($attr->conflicts)) {
                    foreach (explode(',',$attr->conflicts) as $conflict) {
                        if (isset($vars->$conflict)) {                          //Queue twilight zone music...
                            $errors[] = 'Conflict detected, '.$attribute.' and '.$conflict.' are mutually exclusive'." on line number ".$lineNumber.', choose one'; 
                        }
                    }
                }
            }
        } else {
            $errors[] = "No validation scheme found for ".$node." on line number ".$lineNumber;
        }
        return $errors;
    }
    
    /**
     * If a child tag is required by the parent, this routine makes sure it exists
     * 
     * @param type $tag
     * @param type $tags
     * @param type $children
     * @param type $validator
     * @param type $lineNumber
     * @param string $errors
     * @return string
     */
    private static function requiredTagCheck($tag,$tags,$children,$validator,$lineNumber,$errors) {
        $req = [];
        foreach ($tags as $tag => $node) {
            $attrs = $node->attributes();
            foreach ($attrs as $attr => $val) {
                if ($attr == 'required') {
                    $req[] = (string)$val;
                }
            }
            
        }
        if (count($req)) {
            foreach ($req as $val) {
                foreach (explode(',',$val) as $required) {
                    $found = false;
                    foreach ($children as $child => $status) {
                        $found = $found || ($child==$required);
                    } 
                    if (!$found) {
                        $errors[] = 'On line '.$lineNumber.', '.strtoupper($required).' is a required child for '.strtoupper($tag).' but was not found';
                    }
                }
            }
        }
        return $errors;
    }
    
    /**
     * Makes sure there are no nodes out of place, or unknown nodes
     * 
     * @param type $parent
     * @param type $nodes
     * @param type $structure
     * @param type $validator
     * @param type $errors
     * @return type
     */
    private static function checkControllerNodes($parent,$nodes,$structure,$validator,$errors) {
        foreach ($nodes as $index1 => $children) {
            foreach ($children as $node => $child) {
                if (isset($child['attributes']) && count($child['attributes'])) {
                    $errors = self::tagAttributeCheck($parent,$node,$child['attributes'],$validator,$child['lineNumber'],$errors);
                }
                if (isset($child['children']) && count($child['children'])) {
                    $candidates = [];
                    foreach ($child['children'] as $i => $tags) {
                        foreach ($tags as $tag => $data) {
                            $candidates[$tag] = true;
                        }
                    } 
                    $errors = self::requiredTagCheck($node,$structure->$node,$candidates,$validator,$child['lineNumber'],$errors);                    
                    foreach ($child['children'] as $index2 => $tags) {
                        foreach ($tags as $tag => $properties) {
                            if (!isset($structure->$node->$tag)) {
                                 $errors[] = 'Tag '.strtoupper($tag).' is not a valid child of '.strtoupper($node)." on line number ".$properties['lineNumber'];
                                 continue;
                            }

                        }
                    }
                    $errors = self::checkControllerNodes($child,$child['children'],$structure,$validator,$errors);
                } 
            }
        }
        return $errors;
    }

    /**
     * Object to array conversion... for like the 300 millionth time
     * 
     * @param type $attributes
     * @return type
     */
    private static function hashAttributes($attributes=false) {
        $attrs = [];
        foreach ($attributes as $attribute) {
            $attrs[$attribute->nodeName] = $attribute->nodeValue;
        }
        return $attrs;
    }

    /**
     * Recursion... you either love it or hate it...
     * 
     * @param type $dom
     * @return type
     */
    private static function recurseControllerNodes($dom=[]) {
        $struct = [];
        if ($dom->hasChildNodes()) {
            foreach ($dom->childNodes as $idx => $node) {
                if (isset($node->tagName)) {
                    $tagName = $node->tagName;
                    $tag     = [
                        $tagName => [
                            "lineNumber" => $node->getLineNo(),
                            "attributes" => [],
                            "children"   => []
                        ]
                    ];
                    $tag[$tagName]['attributes']  = ($node->hasAttributes()) ? self::hashAttributes($node->attributes) : [];
                    $tag[$tagName]['children']    = ($node->hasChildNodes()) ? self::recurseControllerNodes($node)     : [];
                    $struct[]                     = $tag;
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
    public static function syntaxCheck($namespace=false,$controller=false) {
        $args       = self::arguments();
        $errors     = [];
        if ($module = \Humble::module($args['ns'])) {
            if (file_exists($file = 'Code/'.$module['package'].'/'.$module['controllers'].'/'.str_replace('.xml','',$args['cn']).'.xml')) {
                $dom        = new DOMDocument();
                libxml_use_internal_errors(true);
                if (($xml = @$dom->loadXML(file_get_contents($file)))===false) {
                    print("Errors exist\n");
                    $errors = libxml_get_errors();
                    foreach ($errors as $error) {
                        // You can log the error or display a user-friendly message
                        echo "* ", $error->message, " on line ", $error->line, "\n";
                    }
                } else {
                    $struct     = self::recurseControllerNodes($dom->firstChild);
                    $source     = simplexml_load_file($file);
                    $structure  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Structure.xml');
                    $validator  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Attributes.xml');
                    $errors     = self::checkControllerNodes('controller',$struct,$structure,$validator,$errors);
                }

            } else {
                $errors[]   = "Controller not found";
            }
        } else {
            $errors[] = "Module not found";
        }
        return $errors;
    }
}