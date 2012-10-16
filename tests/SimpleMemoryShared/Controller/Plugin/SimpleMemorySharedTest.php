<?php

/**
 * sudo memcached -d -u nobody -m 128 127.0.0.1 -p 11211 // to run memcached for tests
 */

namespace SimpleMemorySharedTest;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager;

class SimpleMemorySharedTest extends TestCase
{
    protected $sm;
    
    protected $broker;
    
    protected $plugin;
    
    public function setUp()
    {
        require_once __DIR__ . '/../../../../Module.php';
        $module = new \SimpleMemoryShared\Module();
        $serviceConfig = $module->getServiceConfig();
        $config = include __DIR__ . '/../../../../config/module.config.php';
        $this->sm = new ServiceManager\ServiceManager(new ServiceManager\Config($serviceConfig));
        $this->sm->setService('Config', $config);
        $this->sm->setAllowOverride(true);
        
        $helperConfig = $module->getControllerPluginConfig();
        $this->broker = new MockHelper(new ServiceManager\Config($helperConfig));
        $this->broker->setServiceLocator($this->sm);
    }
    
    public function testCanUseFactory()
    {
        $manager = $this->broker->get('memoryshared');
        $this->assertEquals('SimpleMemoryShared\Controller\Plugin\SimpleMemoryShared', get_class($manager));
        $this->assertEquals('SimpleMemoryShared\MemorySharedManager', get_class($manager()));
        $this->assertEquals($manager->getMemorySharedManager(), $manager());
    }
    
    public function testCanSetStorage()
    {
        $manager = $this->broker->get('memoryshared');
        $manager('file');
        $this->assertEquals(get_class($manager()->getStorage()), 'SimpleMemoryShared\Storage\File');
        $manager('segment');
        $this->assertEquals(get_class($manager()->getStorage()), 'SimpleMemoryShared\Storage\Segment');
    }
}

class MockHelper extends ServiceManager\AbstractPluginManager
{
    public function validatePlugin($plugin) {}
}