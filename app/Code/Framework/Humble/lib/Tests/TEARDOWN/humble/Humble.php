<?php
/**
 * Humble Framework Test Harness Cleanup
 *
 * For use when you need to clear the environment after executing the Unit Tests
 *
 * Version 3.0.20160618
 *
 * The interface is defined in Harness.php
 *
 */
namespace tests\TEARDOWN;

class Humble implements \HarnessTeardown {

    public function __construct() {

    }

    /**
     * This method will be called to cleanup anything created during the connect.xml run
     */
    public function execute() {

      //  print("I am in Teardown\n\n");
    }
}