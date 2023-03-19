<?php
/*
            ,--. ,--.        ,--.  ,--.      ,--------.              ,--.
            |  | |  |,--,--, `--',-'  '-.    '--.  .--',---.  ,---.,-'  '-.
            |  | |  ||      \,--.'-.  .-'       |  |  | .-. :(  .-''-.  .-'
            '  '-'  '|  ||  ||  |  |  |         |  |  \   --..-'  `) |  |
             `-----' `--''--'`--'  `--'         `--'   `----'`----'  `--'

 Version 1.0

 */
require "Humble.php";

use PHPUnit\Framework\TestCase;

class MongoTest extends TestCase {

    private $collection = null;

    /**
     *
     * @return string
     */
    public function testConnect() {
        $this->collection = \Humble::collection('core/test');
        $this->assertTrue($this->collection !== null);
        return 'connect';
    }

    /**
     * @depends testConnect
     */
    public function testWrite() {
        $doc = [
            'id' => 1,
            'data' => 'test record'
        ];
        $this->collection = \Humble::collection('core/test');
        $data = $this->collection->add($doc);
        $this->assertTrue(isset($data['_id']));
        return 'write';
    }

    /**
     * @depends testWrite
     */
    public function testRead() {
        $this->collection = \Humble::collection('core/test');
        $this->collection->setId(1);
        $row = $this->collection->findOne();
        $this->assertNotEmpty($row);
        return 'read';
    }

    /**
     * @depends testWrite
     */
    public function testRemove() {
        $this->collection = \Humble::collection('core/test');
        $this->collection->setId(1);
        $this->collection->remove();
        $this->collection->reset();
        $this->collection->setId(1);
        $row = $this->collection->findOne();
        $this->assertEmpty($row);
        return 'remove';
    }

    /**
     * Clears out any rows I may have added, and then drops the collection
     */
    public function __destruct() {
        $this->collection = \Humble::collection('core/test');
        $this->collection->truncate();
        $this->collection->drop();
    }
}
?>