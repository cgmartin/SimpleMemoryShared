<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared\Storage;

use SimpleMemoryShared\Storage\Exception\RuntimeException;

class Apc implements CapacityStorageInterface
{
    /**
     * Construct apc storge
     */
    public function __construct()
    {
        if (!extension_loaded('apc')) {
            throw new RuntimeException('APC extension must be loaded.');
        }
    }

    /**
     * Memory alloc
     */
    public function alloc()
    {
        return;
    }

    /**
     * Read datas with $uid key
     * @param mixed $uid
     * @return mixed
     */
    public function read($uid)
    {
        $this->alloc();
        return apc_fetch($uid);
    }

    /**
     * Write datas on $uid key
     * @param mixed $uid
     * @param mixed $mixed
     */
    public function write($uid, $mixed)
    {
        $this->alloc();
        return apc_store($uid, $mixed);
    }

    /**
     * Close apc
     * @param int
     */
    public function close()
    {
        return;
    }

     /**
     * Get max bloc allow
     */
    public function canAllowBlocsMemory($numBloc)
    {
        return true; // no limitation
    }
}
