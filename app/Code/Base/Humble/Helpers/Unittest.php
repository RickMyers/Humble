<?php
namespace Code\Base\Humble\Helpers;
use Humble;
use Log;
use Environment;
/**    
 *
 * Unit Tests Helper
 *
 * Methods needed for handling unit test execution and display
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Other
 * @author     Richard Myers rick@enicity.com
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0.1
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Unittest extends Directory
{

    private $tests      = false;
    private $order      = false;
    private $source     = false;
    private $results    = false;
    private $xref       = [];

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
     * Returns the individual tests to be run on a  per package basis
     *
     * @return array
     */
    public function fetchTests($package) {
        if (!$this->tests) {
            $this->load();
        }
        return $this->tests->packages->$package->test;
    }

    /**
     * Returns the specific results of a unit test, as well as grades, or "scores" the test
     *
     * @param string $package
     * @param string $class
     * @return array
     */
    public function fetchTestResult($package,$class) {
        $result = [];
        if (isset($this->xref[(string)$package]) && isset($this->xref[(string)$package][(string)$class])) {
            $result     = $this->xref[(string)$package][(string)$class];
            $score      = 0;
            $risky      = false;
            $skipped    = false;
            foreach ($result['results'] as $test_result) {
                switch ($test_result) {
                    case "W"    : $score = $score > 1 ? $score : 1;
                        break;
                    case "E"    : $score = $score > 2 ? $score : 2;
                        break;
                    case "F"    : $score = $score > 3 ? $score : 3;
                        break;
                    case 'R'    : $risky = true;
                        break;
                    case 'S'    : $skipped = true;
                        break;
                    case "."    : //A-OK
                    default     :
                        break;
                }
            }
            $result['score']    = $score;
            $result['risky']    = $risky;
            $result['skpped']   = $skipped;
        }
        return $result;
    }

    /**
     * Returns the order the packages need to be executed in
     *
     * @return array
     */
    public function packageOrder() {
        if (!$this->order) {
            $this->load();
        }
        return $this->order;
    }

    /**
     * Loads up the order of the packages to be executed and the unit tests themselves
     *
     * @return boolean
     */
    public function load() {
        if ($this->source = $this->getSource()) {
            exec('php Harness.php --o source='.$this->source.' output=JSON',$results);
            if (isset($results[0])) {
                $this->order = json_decode($results[0]);
            }
            if (file_exists($this->source)) {
                $this->tests = simplexml_load_file($this->source);
            }
        }
        return ($this->tests && $this->order);
    }

    /**
     * Will run the tests contained in the current "source" driver file
     *
     * @return boolean
     */
    public function run() {
        if ($this->load()) {
            $s = $this->source;
            exec('php Harness.php --x source='.$this->source.' output=JSON',$results);
            if (isset($results[0])) {
                $this->results = json_decode($results[0]);
                foreach ($this->results as $idx => $result) {
                    if (!isset($this->xref[$result->package])) {
                        $this->xref[$result->package] = [];
                    }
                    $this->xref[$result->package][$result->class] = (array)$result;
                }
            }
        }
        return ($this->results !== false);
    }
}