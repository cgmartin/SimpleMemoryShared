<?php

require_once __DIR__ . '/../../zf2/library/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'autoregister_zf' => true,
        'namespaces' => array(
            'SimpleMemoryShared' => __DIR__ . '/../src/SimpleMemoryShared',
            'SimpleMemorySharedTest' => __DIR__ . '/SimpleMemoryShared',
        ),
    ),
));