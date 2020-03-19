<?php
namespace Code\Base\Paradigm\Helpers;
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
 * @author     Original Author <rick@humblecoding.com>
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://license.humblecoding.com
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
        $xml = $this->getDictionaryDefinition();
        $xmlobj = @simplexml_load_string($xml);
        if ($xmlobj) {
        if (isset($xmlobj->suggestion)) {
            $this->setSuggestions($xmlobj->suggestion);
        } else {
            $xml    = (string)substr($xml,strpos($xml,'<entry '));
            $xml    = substr($xml,0,strpos($xml,'</entry>')+8);

            $this->setOriginalTerm($this->extractTag($xml,'ew'));
            $this->setRootWord($this->extractTag($xml,'hw'));
            $this->setFirstUsed($this->extractTag($xml,'date'));
            $this->setPronunciation($this->extractTag($xml,'pr'));
            $this->setWordType($this->extractTag($xml,'fl'));
            $this->setWordOrigin($this->extractTag($xml,'et'));
            $this->setText($this->translateDefinition($this->extractTag($xml,'def')));
            $this->setSynonyms($this->extractTag($xml,'pt'));
            $this->setEnunciation($this->extractTag($xml,'wav'));
            $this->setOrly($this->extractTag($xml,'uro'));
        }
        } else {
            $this->setText("No Data");
        }
        return $this;
    }

}
?>