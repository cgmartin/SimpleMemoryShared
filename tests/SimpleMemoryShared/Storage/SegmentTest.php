<?php

/**
 * sudo memcached -d -u nobody -m 128 127.0.0.1 -p 11211 // to run memcached for tests
 */

namespace SimpleMemorySharedTest\Storage;

use PHPUnit_Framework_TestCase as TestCase;
use SimpleMemoryShared\Storage;

class SegmentTest extends TestCase
{
    protected $storage;
    
    public function setUp()
    {
        $this->storage = new Storage\Segment('S');
    }
    
    public function testCanWriteAndRead()
    {
        $this->storage->write(3, 'sample');
        $datas = $this->storage->read(3);
        $this->assertEquals($datas, 'sample');
    }
    
    public function testCanWriteAndReadWithNumericKey()
    {
        $this->storage->write('1', 'foo');
        $datas = $this->storage->read('1');
        $this->assertEquals($datas, 'foo');
    }
    
    public function testCannotWriteAndReadWithStringKey()
    {
        $this->setExpectedException('SimpleMemoryShared\Storage\Exception\RuntimeException');
        $this->storage->write('custom-key', 'sample');
        $datas = $this->storage->read('custom-key');
        $this->assertEquals($datas, 'sample');
    }
}