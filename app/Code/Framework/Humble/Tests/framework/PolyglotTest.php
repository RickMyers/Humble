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

class PolyglotTest extends TestCase {


    /**
     *
     */
    public function testWrite() {
        $this->assertTrue(true);
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