<?php
/**
 * Humble Framework Test Harness Initializer
 *
 * For use when you need to prep the environment before executing the Unit Tests
 *
 * Version 3.0.20160618
 *
 * The interface is defined in Harness.php
 *
 */
namespace tests\STANDUP;

class Humble implements \HarnessStandup {

    public function __construct() {

    }

    /**
     * This method will be called to create or "standup" anything required during the connect.xml run
     */
    public function execute() {
      //  print("I am in Standup\n\n");
    }
}