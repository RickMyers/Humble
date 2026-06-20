<?php
require_once 'CLI/CLI.php';
class Component extends CLI 
{
 
    /**
     * Compiles a controller
     * 
     * @return type
     */
    public static function compile() {
        $args       = self::arguments();
        $file       = $args['file'];
        print($file."\n");
        $compiler   = \Environment::getCompiler();
        $compiler->compileFile($file);
        return self;
    }

    /**
     * Builds a controller
     * 
     * @return type
     */
    public static function build() {
        $util = Humble::model('admin/utility',true);
        foreach (self::arguments() as $field => $value) {
            $method  = 'set'.self::underscoreToCamelCase($field,true);
            $util->$method($value);
        }
        $use_landing = $util->getLanding() ? true : false;
        $util->createController($use_landing,true);
        return self;
    }
    
    /**
     * Builds a component, such as Model, Entity, or Helper
     */
    public static function create() {
        $args       = self::arguments();
        //@TODO: This...
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
                if (!isset($schema->$attribute)) {
                    $errors[] = $attribute." is not a valid attribute of ".$node." on line number ".$lineNumber;
                    continue;
                }
                if (isset($schema->$attribute->values)) {
                    $parsed_value = explode('=',(string)$value);
                    $parsed       = strtolower($parsed_value[0]);
                    if (!isset($schema->$attribute->values->$parsed)) {
                        $errors[] = $value." is not a valid value for ".$attribute." on line number ".$lineNumber;
                    } else {
                        $attr = $schema->$attribute->values->$parsed->attributes();
                        if (isset($attr->format)) {
                            if (!isset($parsed_value[1])) {
                                $errors[] =  "A value is required for ".$attribute." on line number ".$lineNumber;                                
                            } else {
                                switch ((string)$attr->format) {
                                    case '#' :
                                            if (!is_numeric($parsed_value[1])) {
                                                $errors[] = $parsed_value[1].' is not valid numeric datatype for '.$parsed_value[0].' on line number '.$lineNumber;
                                            }
                                        break;
                                    case 'A' :
                                            if (is_numeric) {
                                                $errors[] = $parsed_value[1].' is not valid string datatype for '.$parsed_value[0].' on line number '.$lineNumber;
                                            }
                                        break;
                                    default  : 
                                        break;
                                }
                            }
                        }
                    }
                } else if (isset($schema->$attribute->options)) {
                    $parsed_value = explode('=',(string)$value);
                    if (isset($parsed_value[1])) {
                        $option       = strtolower($parsed_value[1]);
                        if (!isset($schema->$attribute->options->$option)) {
                            $valid = [];
                            foreach ($schema->$attribute->options as $valid_options) {
                                foreach ($valid_options as $opt => $s) {
                                    $valid[] = $opt;
                                }
                            }
                            $errors[] = $option.' is not a valid value for '.$parsed_value[0].' on line number '.$lineNumber.'. Valid options are ['.implode(',',$valid).'].';
                        }
                    }
                }
                $attr = $schema->$attribute->values->attributes();
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
/*
 <tags>
    <controller taxonomy="control">
        <attributes>
            <name required="true"   purpose="" default="" />
            <use  required="false"  purpose="Default templater found in Application.xml file" default="">
                <values>
                    <twig />
                    <smarty />
                    <latte />
                    <blade />
                    <savant />
                    <mustache />
                    <phptal />
                    <tbs />
                    <php />
                    <rain />
                </values>
            </use>
        </attributes>
    </controller>
 */    
    public static function expandAliases($validator) {
        //print_r($validator);
        print("\n\n=======================================================\n\n");
        foreach ($validator as $base_node => $parameters) {
            print($base_node."\n");
            if (isset($parameters->attributes)) {
                foreach ($parameters as $parm => $options) {
                    print('parms '. $parm."\n");
                    foreach ($options as $value => $opts) {
                        print('value '.$value."\n");
                        foreach ($opts as $val => $parms) {
                            print('val '.$val."\n");
                            foreach ($parms as $parm => $attr) {
                                print('parm '.$parm."\n");
                                if ($attrs = $attr->attributes()) {
                                    if (isset($attrs->alias) || isset($attrs->aliases)) {
                                        $aliases = isset($attrs->alias) ? (string)$attrs->alias : (string)$attrs->aliases;
                                        print($aliases."\n");
                                        
                                        foreach (explode(',',$aliases) as $alias) {
                                            if ($alias) {
                                                //print_r($validator->$base_node->$parm->attributes->$value);print("\n");
                                                //$validator->$base_node->attributes->$parm->$value->$val->$alias = true;
                                            }
                                        }
                                        die('aliases'."\n");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        print_r($validator);
        die();
        return $validator;
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
                libxml_use_internal_errors(true);
                if ($xml = @$dom->loadXML(file_get_contents($file))===false) {
                    $err = libxml_get_errors();
                    foreach ($err as $error) {
                        $errors[] = $error->message. " on line ". $error->line;
                    }
                } else {
                    $structure  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Structure.xml');
                    $validator  = simplexml_load_file('Code/Framework/Humble/lib/syntax/Attributes.xml');
                    foreach ($dom->childNodes as $idx => $node) {
                        if (isset($node->tagName) && ($node->tagName == 'controller')) {
                            $errors     = self::checkControllerNodes($node->tagName,self::recurseControllerNodes($node),$structure,$validator,$errors);
                        }
                    }
                }
            } else {
                $errors[]   = "Controller not found";
            }
        } else {
            $errors[] = "Module not found";
        }
        return (isset($args['out']) && (strtoupper($args['out'])=='JSON')) ? str_replace("\n","",json_encode($errors)) : $errors;
    }

    /**
     * For when you want to call it from a method instead of the CLI
     * 
     * @param type $namespace
     * @param type $controller
     * @param type $output
     * @return type
     */
    public static function check($namespace=false,$controller=false,$output='') {
        if ($namespace && $controller) {
            self::arguments([
                'ns'    => $namespace,
                'cn'    => $controller,
                'out'   => $output
            ]);
            return self::syntaxCheck();
        }
    }
    
    /**
     * Creates the basic form edits JSON template
     */
    public static function createEdits() {
        $tmpl       = 'Code/Framework/Humble/lib/sample/component/edits.json';
        if (file_exists($tmpl)) {
            $args       = self::arguments();
            $errors     = [];
            $form_name  = $args['fm'];
            $namespace  = $args['ns'];
            $alias      = $args['al'];
            $output     = str_replace(['.json','.JSON'],['',''],$args['fi']);
            if ($module = \Humble::module($namespace)) {
                $cfg    = 'Code/'.$module['package'].'/'.$module['module'].'/etc/config.xml';
                $source = str_replace(['&&form_name&&','&&alias&&'],[$form_name,$alias],file_get_contents($tmpl));
                $config = simplexml_load_file($cfg);
                $dest   = $module['module'].'/web/edits/'.$output.'.json';
                $out    = 'Code/'.$module['package'].'/'.$dest;
                if (file_exists($out)) {
                    die('Aborting, the edits file ['.$out.'] already exists!'."\n");
                }
                file_put_contents($out,$source);
                print('Edit   file created at '.$out."\n");
                $config->$namespace->web->edits->addChild($alias,$dest);
                $dom = new DOMDocument("1.0");
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($config->asXML());
                file_put_contents($cfg,$dom->saveXML());
                print('Config file updated at '.$cfg."\n");
            }
        } else {
            die('Source template ['.$tmpl.'] Not found, aborting'."\n");
        }
    }
    
    /**
     * A basic check of the RPC Yaml file per namespace
     */
    public static function yamlCheck() {
        $args = self::arguments();
        if ($ns = $args['namespace'] ?? false) {
            
        }
    }
}