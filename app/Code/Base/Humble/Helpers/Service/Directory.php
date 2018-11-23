<?php
namespace Code\Base\Humble\Helpers\Service;
/**
 *
 * Creates a directory of available services
 *
 * Directory functions
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-Core_Helper_Directory.html
 * @since      File available since Version 1.0.1
 */
class Directory extends \Code\Base\Humble\Helpers\Directory
{

    private $extensions     = array(
        'Smarty3' => '.tpl',
        'Smarty2' => '.tpl',
        'Twig'    => '.twig',
        'PHP'     => '.php'
    );
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
     * This method creates an array that contains information for the view to build the list of directory services
     *
     * @return array
     */
    public function generate() {
        $packages     = \Humble::getPackages();
        $service      = \Humble::getEntity('humble/services');
        $serviceParms = \Humble::getEntity('humble/services/parameters');
        $service->truncate();
        $serviceParms->truncate();
        foreach ($packages as $package) {
            $modules = \Humble::getModules($package);
            foreach ($modules as $module) {
                $mod         = \Humble::getModule($module);
                $controllers = $mod['controller'];
                $views       = $mod['views'];
                $directory   = 'Code/'.str_replace('_','/',$package.'_'.$controllers);
                $viewsDir    = 'Code/'.str_replace('_','/',$package.'_'.$views);
                if (!is_dir($directory)) {
                    continue;
                }
                $dir = dir($directory);
                $service->setNamespace($module);
                while (($entry = $dir->read())!== false) {
                     if (($entry == '.') || ($entry == '..')) {
                        continue;
                    }
                    $controller = $directory.'/'.$entry;

                    if (!is_dir($controller) && (file_exists($controller)) && $this->isValidXML($xml = file_get_contents($controller))){
                        $service->setRouter($this->getBaseFileName($entry));
                        $code    = new \SimpleXMLElement($xml);
                        $cntAttr = $code->attributes();
                        $tmplate = $cntAttr['use'];

                        foreach ($code->actions as $idx => $action) {
                            $parameters = [];
                            foreach ($action as $data) {

                                $actAttr     = $data->attributes();
                                $service->setAuthorized((isset($actAttr['authorization']) && (strtolower($actAttr['authorization'])==='true')) ? "Y" : "N");
                                $service->setService($actAttr['name']);
                                $service->setOutput((isset($actAttr['output'])  ? $actAttr['output'] : 'HTML'));
                                foreach ($data as $node => $tag) {
                                    if ($node == "description") {
                                        $service->setDescription($tag);
                                    }
                                }
                                $vd = $viewsDir.'/'.$service->getRouter();
                                if (is_dir($vd)) {
                                    if (isset($this->extensions[(string)$tmplate])) {
                                        $ext      = $this->extensions[(string)$tmplate];
                                        $viewFile = $vd.'/'.$tmplate.'/'.$service->getService().$ext;
                                        if (file_exists($viewFile)) {
                                            $service->setView('Y');
                                        } else {
                                            $service->setView('N');
                                        }
                                    }
                                }
                                $service->setUrl($service->getNamespace().'/'.$service->getRouter().'/'.$service->getService());
                                $id = $service->add();

                                foreach ($data as $node => $parts) {
                                    if (($node === "entity") || ($node === "helper") || ($node === "model") || ($node === "mongo")) {
                                        foreach ($parts as $parm => $parameter) {
                                            $attr       = $parameter->attributes();
                                            $name       = (string)$attr['name'];
                                            $type       = isset($attr['type'])        ? (string)$attr['type'] : "*";
                                            $source     = isset($attr['source'])      ? (string)$attr['source'] : "???";
                                            $value      = isset($attr['value'])       ? (string)$attr['value'] : $name;
                                            $required   = (isset($attr['required']) && (strtolower($attr['required'])==="true"))    ? "Y" : "N";
                                            $default    = isset($attr['default'])     ? (string)$attr['default'] : "";
                                            $parameters[$name] = array(
                                                "name" => $name,
                                                "type" => $type,
                                                "value" => $value,
                                                "source" => $source,
                                                "required" => $required,
                                                "default" => $default
                                            );
                                        }
                                    }
                                }
                                $serviceParms->setServiceId($id);
                                foreach ($parameters as $name => $parmData) {
                                    $serviceParms->setParameter($name);
                                    $serviceParms->setValue($parmData['value']);
                                    $serviceParms->setSource($parmData['source']);
                                    $serviceParms->setDatatype($parmData['type']);
                                    $serviceParms->setDefault($parmData['default']);
                                    $serviceParms->setRequired($parmData['required']);
                                    $serviceParms->add();
                                }
                            }
                        }
                    }
                }
            }
        }

        return 'Generated!';
    }
}

