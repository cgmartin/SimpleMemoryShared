<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared\Storage;

use SimpleMemoryShared\Storage\Exception\RuntimeException;

class Memcached implements CapacityStorageInterface
{
    /**
     * Memcached instance
     * @var mixed
     */
    protected $memcached;

    /**
     * Default config
     * @var array
     */
    protected $config = array(
        'host' => '127.0.0.1',
        'port' => 11211,
    );

    /**
     * Construct memcached storge
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if (!extension_loaded('memcache')) {
            throw new RuntimeException('Memcache extension must be loaded.');
        }
        if($config) {
            $this->config = $config;
        }
    }

    /**
     * Memory alloc
     */
    public function alloc()
    {
        if(null !== $this->memcached) {
            return;
        }
        $this->memcached = new \Memcache('fork_pool');
        $connexion = @$this->memcached->connect($this->config['host'], $this->config['port']);
        if(!$connexion) {
            throw new RuntimeException('Connexion to memcache refused.');
        }
    }

    /**
     * Read datas with $uid key
     * @param mixed $uid
     * @return mixed
     */
    public function read($uid)
    {
        $this->alloc();
        return $this->memcached->get($uid);
    }

    /**
     * Write datas on $uid key
     * @param mixed $uid
     * @param mixed $mixed
     */
    public function write($uid, $mixed)
    {
        $this->alloc();
        return $this->memcached->set($uid, $mixed);
    }

    /**
     * Close segment
     * @param int
     */
    public function close()
    {
        if(null === $this->memcached) {
            return;
        }
        $this->memcached->flush();
        $this->memcached->close();
    }

     /**
     * Get max bloc allow
     */
    public function canAllowBlocsMemory($numBloc)
    {
        return true; // no limitation
    }
}
