<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface,
        ServiceProviderInterface, ControllerPluginProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/config/autoload_classmap.php'
            ),
        );
    }
    
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'MemoryShared' => function($sm) {
                    $memorySharedManager = $sm->getserviceLocator()->get('MemorySharedManager');
                    $managerHelper = new Controller\Plugin\SimpleMemoryShared();
                    $managerHelper->setMemorySharedManager($memorySharedManager);
                    return $managerHelper;
                }
            ),
            'aliases' => array(
                'MemorySharedManager' => 'MemoryShared',
                'SimpleMemoryShared' => 'MemoryShared',
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'MemorySharedManager' => function($sm) {
                    $memorySharedManager = new MemorySharedManager();
                    if(isset($config['simple_memory_shared']['default_storage'])) {
                        $defaultStorage = $config['simple_memory_shared']['default_storage'];
                        $pluginManager = $memorySharedManager->getStoragePluginManager();
                        $storage = $pluginManager->get($defaultStorage['type'], $defaultStorage['options']);
                        $memorySharedManager->setStorage($storage);
                    }
                    return $memorySharedManager;
                },
            ),
            'aliases' => array(
                'SimpleMemoryShared' => 'MemorySharedManager',
            ),
        );
    }
}
