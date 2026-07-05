<?php
namespace Code\Framework\Admin\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * Command Line Interface Helper
 *
 * CLI Helper
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick rick@humbleprogramming.com
 */
class Cli extends Helper
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
    public function className() {
        return __CLASS__;
    }
    
    /**
     * Parses the option out into something readable
     * 
     * @param type $topic
     * @return string
     */
    public function parseTopic($topic=false) : string {
        $parsed = '';
        if ($topic = ($topic) ? $topic : ($this->getTopic() ? $this->getTopic() : false)) {
            $parts = explode('|',$topic);
            $cnt   = count($parts);
            if ($cnt > 1) {
                for ($i=0; $i<$cnt; $i++) {
                    if ($i === ($cnt-1)) {
                        $parsed .= ' or --'.$parts[$i];
                    } else {
                        $parsed .= '--'.$parts[$i].', ';
                    }
                }
            } else {
                $parsed = $parts[0];
            }
        }
        return $parsed;
    }
    
    /**
     * If there are aliases for a parameter/command, return only the first
     * 
     * @param type $command
     * @return type
     */
    public function parseParameter($parameter=false) {
        $parm='';
        if ($parameter) {
            $parm = explode('|',$parameter)[0];
        }
        return $parm;
    }
    
    /**
     * Parse and prepare the command structure for presentation
     * 
     * @param type $struct
     * @return $this
     */
    public function parseCommandStructure($struct=[]) {
        if ($struct) {
            $this->setDescription($struct['description'] ?? 'N/A');
            $this->setLinuxExample($struct['usage']['linux'] ?? 'N/A');
            $this->setWindowsExample($struct['usage']['windows'] ?? 'N/A');
            $this->setFunction($struct['function'] ?? 'N/A');
            $this->setTitle($struct['title'] ?? 'N/A');
            $this->setDirective((isset($struct['directive']) && $struct['directive']) ? 'Yes' : 'No');
            $this->setExtendedDescription($struct['extended'] ?? 'N/A');
            $this->setDocumentation($struct['documentation'] ?? 'N/A');
            $this->setYouTube($struct['youtube'] ?? 'N/A');
            $this->setRequiredParameters($struct['parameters']['required'] ?? []);
            $this->setOptionalParameters($struct['parameters']['optional'] ?? []);
        }
        return $struct;
    }
    
    /**
     * Extracts the specific command structure from the list of all CLI commands
     * 
     * @param type $commands
     * @return array
     */
    public function commandStructure($commands=[]) {
        $data     = [];
        $category = $this->getCategory();
        $topic    = $this->getTopic();
        if ($category && $topic) {
            if (isset($commands[$category])) {
                $data = (isset($commands[$category][$topic]) ? $commands[$category][$topic] : [] );
            }
        }
        return $data;
    }
}