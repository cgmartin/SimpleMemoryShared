<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface,
        ServiceProviderInterface
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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'MemorySharedManager' => function($sm) {
                    $mmorySharedManager = new MemorySharedManager();
                    if(isset($config['simple_memory_shared']['default_storage'])) {
                        $defaultStorage = $config['simple_memory_shared']['default_storage'];
                        $pluginManager = $mmorySharedManager->getStoragePluginManager();
                        $storage = $pluginManager->get($defaultStorage['type'], $defaultStorage['options']);
                        $mmorySharedManager->setStorage($storage);
                    }
                    return $mmorySharedManager;
                },
            ),
            'aliases' => array(
                'SimpleMemoryShared' => 'MemorySharedManager',
            ),
        );
    }
}
