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

class MySQLTest extends TestCase {

    /**
     *
     * @return string
     */
    public function testConnect() {
        $this->assertTrue(true);
        return 'connect';
    }

    /**
     * @depends testConnect
     */
    public function testWrite() {
        $this->assertTrue(false);
        return 'write';
    }

    /**
     * @depends testWrite
     */
    public function testRead() {
        $this->assertTrue(true);
        return 'read';
    }

    /**
     * @depends testWrite
     */
    public function testRemove() {
        $this->assertTrue(true);
        return 'remove';
    }
}
?>