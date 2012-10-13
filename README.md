ZF2 SimpleMemoryShared module
============

Version 1.0 Created by [Vincent Blanchon](http://developpeur-zend-framework.fr/)

Introduction
------------

ZF2 module SimpleMemoryShared provide a memory shared manager.
This module can be used alone or with the [ParallelJobs](https://github.com/blanchonvincent/ParallelJobs) module.

You can use this module within yours simply to have a simple memory manager.

Memory shared manager usage
------------

1) Simple share with memory segment for short datas :
    
```php
<?php
$manager = $this->getServiceLocator()->get('MemorySharedManager');
$manager->setStorage('segment');
$manager->write('my-identifier', 'secret');

// in other process, you can do
$object = $manager->read('my-identifier');
```

2) Share object with file storage :
    
```php
<?php
$manager = $this->getServiceLocator()->get('MemorySharedManager');
$manager->setStorage('file');
$manager->write('my-identifier', new MyObject());

// in other process, you can do
$object = $manager->read('my-identifier');
```

3) Share object with memcached storage :
    
```php
<?php
$manager = $this->getServiceLocator()->get('MemorySharedManager');
$manager->setStorage('memcached', array('host' => '127.0.0.1', 'port' => 11211));
$manager->write('my-identifier', new MyObject());

// in other process, you can do
$object = $manager->read('my-identifier');
```