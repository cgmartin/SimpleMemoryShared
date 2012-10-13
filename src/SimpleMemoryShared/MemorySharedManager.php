<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared;

class MemorySharedManager
{
    /**
     *
     * @var Storage\StorageInterface
     */
    protected $storage;

    /**
     *
     * @var StoragePluginManager
     */
    protected $storagePluginManager;

    /**
     * Construt manager with storage
     * @param type $storage
     */
    public function __construct($storage = null)
    {
        if($storage) {
            $this->setStorage($storage);
        }
    }

    /**
     * Get the current storage
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if(null === $this->storage) {
            $this->setStorage(new Storage\File(array('dir' => __DIR__ . '/../../data/')));
        }
        return $this->storage;
    }

    /**
     * Set the current storage
     * @param Storage\StorageInterface/string $storage
     * @return SimpleMemoryShared
     */
    public function setStorage($storage, $options = null)
    {
        if(!$storage instanceof Storage\StorageInterface) {
            $storage = $this->getStoragePluginManager()->get($storage, $options);
        }
        $this->storage = $storage;
        return $this;
    }

    /**
     * Get the storage plugin manager
     * @return StoragePluginManager
     */
    public function getStoragePluginManager()
    {
        if(null === $this->storagePluginManager) {
            $this->setStoragePluginManager(new StoragePluginManager());
        }
        return $this->storagePluginManager;
    }

    /**
     * Set the storage plugin manager
     * @param StoragePluginManager $storagePluginManager
     * @return SimpleMemoryShared
     */
    public function setStoragePluginManager(StoragePluginManager $storagePluginManager)
    {
        $this->storagePluginManager = $storagePluginManager;
        return $this;
    }
}
