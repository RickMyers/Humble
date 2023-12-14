<?php
namespace Code\Framework\Paradigm\Helpers;
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Component
 * @package    Workflow
 * @author     Original Author <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0
 * @since      File available since Version 1.0.1
 */
class Data extends Helper
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Standard required method
     *
     * @return system
     */
    public function getClassName()
    {
        return __CLASS__;
    }

    /**
     * Translates out useless tags from the text
     *
     * @param string $text
     * @return string
     */
    public function translateDefinition($text='') {
        $search = array(
            "<date>",
            "</date>",
            "<sn>",
            "</sn>",
            "<dt>",
            "</dt>",
            "<sx>",
            "</sx>",
            "<it>",
            "</it>",
            "<ssl>",
            "</ssl>",
            "<sd>",
            "</sd>",
            "<vi>",
            "</vi>",
            "<vt>",
            "</vt>"
        );
        $replace = array(
            "First recorded use: ",
            " ",
            "",
            "",
            "Definition: ",
            "",
            "",
            "",
            "",
            "",
            "",
            " ",
            "",
            "",
            "",
            "",
            "",
            ""
        );
        return str_replace($search,$replace,$text);
    }

    /**
     * Strips stuff out of complex XML tags
     *
     * @param string $xml
     * @param string $element
     * @return string
     */
    protected function extractTag($xml,$element) {
        $st     = '<'.$element;
        $et     = '</'.$element.'>';
        $a      = strpos($xml,$st);
        $xml    = (string)substr($xml,strpos($xml,$st)+strlen($st)+1);
        return substr($xml,0,strpos($xml,$et));
    }

    /**
     * Parses the result for a dictionary search
     *
     * @return \Paradigm_Helpers_Data
     */
    public function search() {
        if ($json = json_decode($this->dictionaryDefinition(),true)) {
            $text = '';
            foreach ($json as $entry => $data) {
                if (isset($data['shortdef'])) {

                    foreach ($data['shortdef'] as $def) {
                        $text .= ($text) ? ' / '.$def : $def;
                    }
                    $this->setText($text);
                }
            }
            if (!$text) {
                $this->setSuggestions(implode('/',$json));
            }
        } else {
            $this->setText("No Data");
        }
        return $this;
    }

}
?>