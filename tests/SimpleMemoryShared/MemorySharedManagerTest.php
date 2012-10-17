<?php

/**
 * sudo memcached -d -u nobody -m 128 127.0.0.1 -p 11211 // to run memcached for tests
 */

namespace SimpleMemorySharedTest;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager;

class MemorySharedManagerTest extends TestCase
{
    protected $sm;
    
    public function setUp()
    {
        require_once __DIR__ . '/../../Module.php';
        $module = new \SimpleMemoryShared\Module();
        $serviceConfig = $module->getServiceConfig();
        $config = include __DIR__ . '/../../config/module.config.php';
        $this->sm = new ServiceManager\ServiceManager(new ServiceManager\Config($serviceConfig));
        $this->sm->setService('Config', $config);
        $this->sm->setAllowOverride(true);
    }
    
    public function tearDown()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $manager->getStorage()->close();
    }
    
    public function testCanGetFactory()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $this->assertEquals(get_class($manager), 'SimpleMemoryShared\MemorySharedManager');
        $manager = $this->sm->get('SimpleMemoryShared');
        $this->assertEquals(get_class($manager), 'SimpleMemoryShared\MemorySharedManager');
    }
    
    public function testCanSetStorageWithNickname()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $manager->setStorage('file');
        $storage = $manager->getStorage();
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\File');
        $manager->setStorage('segment');
        $storage = $manager->getStorage();
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Segment');
        $manager->setStorage('memcached');
        $storage = $manager->getStorage();
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Memcached');
    }
    
    public function testCannotSetStorageWithNickname()
    {
        $this->setExpectedException('SimpleMemoryShared\Storage\Exception\RuntimeException');
        $manager = $this->sm->get('MemorySharedManager');
        $manager->setStorage('file', array('dir' => 'unknow'));
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\File');
    }
    
    public function testCanGetPluginWithStorageManager()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('file');
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\File');
        $storage = $manager->getStoragePluginManager()->get('segment');
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Segment');
        $storage = $manager->getStoragePluginManager()->get('memcached');
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Memcached');
    }
    
    public function testCannotGetUnknowPluginWithStorageManager()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('unknow');
    }
    
    public function testCanCreateMemcachedStorageWithCustomConfig()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('memcached', array('host' => '127.0.0.1', 'port' => 11211));
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Memcached');
        $storage->write(__FUNCTION__, 'foo');
        $data = $storage->read(__FUNCTION__, 'foo');
        $this->assertEquals($data, 'foo');
    }
    
    public function testCanCreateFileStorageWithCustomConfig()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('file', array('dir' => __DIR__ . '/tmp'));
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\File');
        $storage->write(__FUNCTION__, 'foo');
        $data = $storage->read(__FUNCTION__, 'foo');
        $this->assertEquals($data, 'foo');
    }
    
    public function testCanCreateSegmentStorageWithCustomConfig()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('segment', array('identifier' => 'E'));
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Segment');
        $storage->write(5, 'foo');
        $data = $storage->read(5, 'foo');
        $this->assertEquals($data, 'foo');
        
        $storage = new \SimpleMemoryShared\Storage\Segment('D');
        $data = $storage->read(5, 'foo');
        $this->assertNotEquals($data, 'foo');
        
        $storage = new \SimpleMemoryShared\Storage\Segment('E');
        $data = $storage->read(5, 'foo');
        $this->assertEquals($data, 'foo');
    }
    
    public function testCanCreateForkWithSegmentStorage()
    {
        $manager = $this->sm->get('MemorySharedManager');
        $storage = $manager->getStoragePluginManager()->get('segment', array('identifier' => 'E'));
        $this->assertEquals(get_class($storage), 'SimpleMemoryShared\Storage\Segment');
        $segmentSize = $storage->getSegmentSize();
        $blocSize = $storage->getBlocSize();
        $allowed = $storage->canAllowBlocsMemory($segmentSize/($blocSize*2));
        $this->assertEquals(true, $allowed);
        $allowed = $storage->canAllowBlocsMemory($segmentSize/($blocSize/2));
        $this->assertEquals(false, $allowed);
        $allowed = $storage->canAllowBlocsMemory(floor($segmentSize/$blocSize));
        $this->assertEquals(true, $allowed);
    }
}