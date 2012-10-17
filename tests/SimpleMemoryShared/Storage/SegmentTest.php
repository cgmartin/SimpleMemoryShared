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
        $this->storage = new Storage\Segment('U');
    }
    
    public function tearDown()
    {
        $this->storage->close();
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
    
    public function testCanWriteAndReadIntValue()
    {
        $this->storage->write('1', 12);
        $datas = $this->storage->read('1');
        $this->assertEquals('string', gettype($datas));
        $datas = (integer)$datas;
        $this->assertEquals($datas, 12);
    }
    
    public function testCanWriteAndReadBooleanValue()
    {
        $this->storage->write('1', true);
        $datas = $this->storage->read('1');
        $this->assertEquals('string', gettype($datas));
        $datas = (boolean)$datas;
        $this->assertEquals($datas, true);
    }
    
    public function testCannotSetBlocSizeWithMemoryAllocated()
    {
        $this->storage->write('1', 12345678910);
        $datas = $this->storage->read('1');
        $this->assertEquals(12345678, $datas);
        $this->setExpectedException('SimpleMemoryShared\Storage\Exception\RuntimeException');
        $this->storage->setBlocSize(16);
    }
    
    public function testCannotGetAccessBadSegment()
    {
        $this->storage->setSegmentSize(8);
        $this->storage->setBlocSize(8);
        
        $this->storage->write('0', 12345678910);
        $datas = $this->storage->read('0');
        $this->assertEquals(12345678, $datas);
        
        $this->setExpectedException('SimpleMemoryShared\Storage\Exception\RuntimeException');
        $this->storage->write(2, 12345678910);
    }
    
    public function testCanReallocMemory()
    {
        $this->storage->setSegmentSize(8);
        $this->storage->setBlocSize(8);
        
        $this->storage->write('0', 12345678910);
        $datas = $this->storage->read('0');
        $this->assertEquals(12345678, $datas);
        
        $this->storage->realloc(64, 8);
        $this->storage->write(2, 12345678910);
        $datas = $this->storage->read(2);
        $this->assertEquals(12345678, $datas);
    }
}