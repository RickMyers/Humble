<?php
/*
            ,--. ,--.        ,--.  ,--.      ,--------.              ,--.
            |  | |  |,--,--, `--',-'  '-.    '--.  .--',---.  ,---.,-'  '-.
            |  | |  ||      \,--.'-.  .-'       |  |  | .-. :(  .-''-.  .-'
            '  '-'  '|  ||  ||  |  |  |         |  |  \   --..-'  `) |  |
             `-----' `--''--'`--'  `--'         `--'   `----'`----'  `--'

 Version 1.0

 */
use PHPUnit\Framework\TestCase;

class RPCTest extends TestCase {

    /**
     *
     * @return string
     */
    public function testRest() {
        $this->assertTrue(true);
        return 'rest';
    }


    /**
     *
     * @return string
     */
    public function testSoap() {
        $this->assertTrue(true);
        return 'soap';
    }

}
?>