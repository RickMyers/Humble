<?php
require_once 'CLI/CLI.php';
class Component extends CLI implements CLIInterface
{
 
    
    private static $trueish  = [
        'Y' => true,
        'TRUE' => true,
        'ON' => true,
        'YES' => true,
        '1' => true,
        1 => true
    ];
    
    /**
     * A quick function to determine if a trueish [Y,YES,TRUE,ON,1] value was passed
     * 
     * @param type $value
     * @return type
     */
    private static function trueish($value=false) {
        return isset(self::$trueish[(string)strtoupper($value)]);
    }
    
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
    }

    /**
     * Builds a controller
     * 
     * @return type
     */
    public static function buildController() {
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
        if (isset($validator->$node)) {                                
            foreach ($validator->$node->attributes as $idx => $schema) {
                //first check for required attributes, or when one attribute requires another
                foreach ($schema as $var => $opts) {
                    $attr = $opts->attributes();
                    if (isset($attr->required) && (self::trueish($attr->required))) {
                        if (!isset($attributes[$var])) {
                            $errors[] = $var." is a required attribute on line number ".$lineNumber; 
                        }
                    }
                    if (isset($attr->requires) && ($attr->requires)) {
                        foreach (explode(",",$attr->requires) as $require) {
                            if (!isset($attributes[$require])) {
                                $errors[] = $require." is a required attribute when ".$var." is present on line number ".$lineNumber;                                
                            }
                        }
                    }                    
                }
                //We need to find the correct syntax scheme to compare the attribute to, since some have multiple schemes depending on parent
                //Still not sure what I am doing here... for a future me to figure out
                if (isset($schema->parent) && ((string)$schema->parent == $parent)) {
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
                    $atrs       = $schema->$attribute->attributes();
                    $vals       = [];
                    $composite  = (isset($atrs->composite) ? (string)$atrs->composite : false);
                    $vals       = ($composite) ? explode($composite,$value) : ($vals[] = $value);
                    $a            = $schema->$attribute->options->attributes();
                    $ignore_case  = (isset($a->ignore_case) && (strtolower($a->ignore_case)==='true'));
                    foreach ($vals as $val) {
                        $parsed_value = explode('=',(string)$val);
                        if (isset($parsed_value[1])) {
                            $option       = ($ignore_case) ? strtolower($parsed_value[1]) : $parsed_value[1];
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
                        $found = $found || ($child == $required);
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
    
    /**
     * Creates and returns a parameter tag
     * 
     * @param type $dom
     * @return type
     */
    private static function injectParameterTag($dom) {
        $parameter = $dom->createElement('parameter');
        foreach ([ 'name' => 'id', 'source' => 'post', 'default' => '' ] as $attr => $value) {
            $parm        = $dom->createAttribute($attr);
            $parm->value = $value;
            $parameter->appendChild($parm);                                
        }        
        return $parameter;
    }
    
    /**
     * Creates and return the mongo ORM tag, adding in the parameter tag
     * 
     * @param type $dom
     * @return type
     */
    protected static function injectMongoTag($dom) {
        $mongo = $dom->createElement('mongo');
        foreach ([ 'namespace' => 'paradigm', 'class' => 'elements', 'id' => 'element' ] as $attr => $value) {
            $parm        = $dom->createAttribute($attr);
            $parm->value = $value;
            $mongo->appendChild($parm);                                
        }
        $mongo->append(self::injectParameterTag($dom));
        return $mongo;
    }
    
    /**
     * We are going to look for the correct action to inject the required workflow component management object
     * 
     * @param nodelist $nodeList
     * @param object $dom
     * @param string $action
     */
    protected static function recurseNodes(&$nodeList,$dom,$action=false) {
        foreach ($nodeList->childNodes as $node) {
            if ($node->localName == 'action') {
                foreach ($node->attributes as $var => $val) {
                    if (($var === 'name') && ($val->nodeValue === $action)) {
                        $node->append(self::injectMongoTag($dom));
                    }
                }
            }
            if ($node && $node->hasChildNodes()) {
                self::recurseNodes($node,$dom,$action);
            }              
        }
    }
    
    /**
     * Checks for the existence of a controller, creating it if need be
     * 
     * @param array $module
     * @param array $uri
     * @return boolean
     */
    protected static function controllerExistsCheck($module=[],$uri=[]) {
        if (!file_exists($controller = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$uri[1].'.xml')) {
            $arguments = ['namespace' => $uri[0],'name' => $uri[1], 'action' => $uri[2], 'engine' => 'Smarty'];          
            print(Humble::exec('Component','buildController',$arguments)."\n");
            //create controller, add the element
        }
        return file_exists($controller);
    }

    /**
     * 
     * @param string $URI (optional);
     */
    public static function componentConfigurationTemplate($URI=false) {
        $args = self::arguments();
        if ($uri  = isset($args['uri']) ? $args['uri'] : ($URI ? $URI : false)) {
            $parts = explode('/',(substr($uri,0,1) === '/') ? substr($uri,1) : $uri);
            if ($module = \Humble::module($parts[0])) {
                if (self::controllerExistsCheck($module,$parts)) {
                    $project    = \Environment::project();
                    $mod        = Humble::module($project->namespace);
                    $file       = file_exists($file = 'Code/'.$mod['package'].'/'.$mod['module'].'/etc/template.tpl') ? $file : 'Code/Framework/Humble/etc/template.tpl';
                    if (!is_dir($dir        = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['views']).'/'.$parts[1].'/Smarty')) {
                        mkdir($dir,0775,true);    
                    }
                    copy($file,$dir.'/'.$parts[2].'.tpl');
                    if (file_exists($controller = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$parts[1].'.xml')) {
                        $xml    = new DOMDocument('1.0');
                        $xml->preserveWhiteSpace = true;
                        $xml->formatOutput       = true; 
                        $xml->load($controller); 
                        foreach ($xml->childNodes as $node) {
                            if ($node && $node->hasChildNodes()) {
                                self::recurseNodes($node,$xml,$parts[2]);
                            }  
                        }
                        $dest   = 'Code/Framework/Admin/Controllers/testie2.xml';
                        $xml->save($dest);                        
                    }
                    //Tailor the controller to include the mongo code necessary to support component configuration
                } else {
                    die("Error while creating controller [".$parts[1]."]\n");
                }
            } else {
                die("Invalid Namespace [".$parts[0]."]\n");
            }
        } else {
            die("Minimum arguments were not passed [uri]\n");
        }
    }

    /**
     * For when you want to call it from a method instead of the CLI
     * 
     * @param string $command
     * @param array $arguments
     * @return string
     */
    public static function run($command,$arguments) {
        if ($command && $arguments) {
            self::arguments($arguments);
            return self::$command();
        }
    }       
}