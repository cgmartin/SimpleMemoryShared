<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared\Storage;

class File implements CapacityStorageInterface
{
    /**
     * Directory storage
     * @var string
     */
    protected $dir;

    /**
     * List of files
     * @var array
     */
    protected $files = array();

    /**
     *
     * @param string $dir
     */
    public function __construct($dir = null)
    {
        if(null === $dir) {
            $dir = __DIR__ . '/../../../data/';
        }
        if(is_array($dir)) {
            if(!isset($dir['dir'])) {
                throw new Exception\RuntimeException(
                    'File storage options must be a directory '
                    . 'name or array with a "dir" key'
                );
            }
            $dir = $dir['dir'];
        }
        if(!file_exists($dir)) {
            throw new Exception\RuntimeException(
                'Directory "' . $dir . '" for the file storage do not exists'
            );
        }
        $this->dir = $dir;
    }

    /**
     * Read datas with $uid key
     * @param mixed $uid
     * @return mixed
     */
    public function read($uid)
    {
        if(!file_exists($this->dir. '/'. $uid)) {
            return false;
        }
        $contents = file_get_contents($this->dir. '/'. $uid);
        return unserialize($contents);
    }

    /**
     * Write datas on $uid key
     * @param mixed $uid
     * @param mixed $mixed
     */
    public function write($uid, $mixed)
    {
        $fp = @fopen($this->dir. '/'. $uid, 'w+');
        if(!$fp) {
            return false;
        }
        $r = fwrite($fp, serialize($mixed));
        fclose($fp);
        $this->files[] = $this->dir. '/'. $uid;
        return $r;
    }

    /**
     * Close storage
     * @param int
     */
    public function close()
    {
        foreach($this->files as $file) {
            @unlink($file);
        }
    }

    /**
     * Get max bloc allow
     * @return int
     */
    public function canAllowBlocsMemory($numBloc)
    {
        return true; // no limitation
    }
}
