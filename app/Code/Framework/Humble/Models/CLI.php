<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * CLI Methods
 *
 * See Title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Myers <rick@humbleprogramming.com>
 */
class CLI extends Model
{

    private $global = false;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $project = \Environment::project();
        $this->global = ($project->name === 'humble');   //If global, list all commands, else restrict some
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function className() {
        return __CLASS__;
    }

    /**
     * Gets the default command line directives
     * 
     * @param type $dh
     * @return type
     */
    public function aggregateDirectories($dh) {
        $available_commands = [];    
        while ($entry = $dh->read()) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (is_dir('CLI/'.$entry)) {
                if (file_exists('CLI/'.$entry.'/directory.yaml')) {
                    $available_commands[$entry] = yaml_parse_file('CLI/'.$entry.'/directory.yaml');
                }
            }
        }
        return $available_commands;
    }
    
    /**
     * Gets custom command line directives
     * 
     * @return array
     */
    public function aggregateModuleCommands() {
        $available_commands = [];        
        foreach ($modules = \Humble::entity('humble/modules')->setEnabled('Y')->setCli('Y')->fetch() as $module) {
            $commands = 'Code/'.$module['package'].'/'.$module['module'].'/CLI/directory.yaml';
            if (file_exists($commands)) {
                $available_commands[ucfirst($module['namespace'])] = yaml_parse_file($commands);
            }
        }
        return $available_commands;    
    }

    public function parseCommand($command=false) {
        $cmd = '';
        if ($command) {
            $cmd = explode('|',$command)[0];
        }
        return $cmd;
    }
    
    /**
     * Groups the base commands with custom commands and removes any non-global commands
     * 
     * @return array
     */
    public function commands() {
        $commands = array_merge_recursive($this->aggregateDirectories(dir('CLI')),$this->aggregateModuleCommands());
        if ($this->global) {
            foreach ($commands as $region => $cmds) {
                foreach ($cmds as $cmd => $options) {
                    if (isset($options['global'])&& !$options['global']) {
                        unset($commands[$region][$cmd]);
                    }
                }
            }
        }
        return $commands;
    }

    /**
     * Manages the passed arguments to the CLI program
     * 
     * @param type $cli
     * @return array
     */
    protected function arguments($cli=false) {
        $arguments = [];
        if ($cli) {
            foreach (['required'=>true,'optional'=>true] as $category => $active) {
                if (isset($cli['parameters'][$category])) {
                    foreach ($cli['parameters'][$category] as $parm => $desc) {
                        $method = 'get'.$this->underscoreToCamelCase($cmd = $this->parseCommand($parm),true);
                        if ($val = $this->$method()) {
                            $arguments[$cmd] = $val;
                        }
                    }
                }
            }
        }
        return $arguments;
    }
    
    /**
     * 
     */
    public function run() {
        $output = '';
        $cmds   = $this->commands();
        if ($cli = $cmds[$this->getCategory()][$this->getTopic()]) {
            $driver     = file_exists($d = \Environment::project('namespace')) ? $d : 'humble'; /* Is the driver the same as namespace? If not, just use the humble driver */
            $command    = './'.$driver." --".$this->getTopic()." ";
            foreach ($this->arguments($cli) as $parm => $val) {
                $command .= $parm.'="'.$val.'" ';
            }
            $result = passthru($command);
        }
        $b = 4;
    }
}