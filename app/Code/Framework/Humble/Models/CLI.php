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
            print($commands."\n");
            if (file_exists($commands)) {
                print("found\n");
                $available_commands[ucfirst($module['namespace'])] = yaml_parse_file($commands);
            }
        }
        return $available_commands;    
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
}