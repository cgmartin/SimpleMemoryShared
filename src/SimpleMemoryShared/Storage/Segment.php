<?php

/*
 * This file is part of the SimpleMemoryShared package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimpleMemoryShared\Storage;

class Segment implements CapacityStorageInterface
{
    /**
     * identifier
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var mixed
     */
    protected $memory;

    /**
     * Bloc size
     * @var int
     */
    protected $segmentSize = 256;

    /**
     * Bloc size
     * @var int
     */
    protected $blocSize = 8;

    /**
     * Construct segment memory
     * @param type $identifier
     */
    public function __construct($identifier = 'Z')
    {
        if(is_array($identifier)) {
            if(!isset($identifier['identifier'])) {
                throw new Exception\RuntimeException(
                    'Segment storage options must be an identifier '
                    . 'name or array with a "identifier" key'
                );
            }
            $identifier = $identifier['identifier'];
        }
        $this->identifier = $identifier;
    }

    public function realloc($segmentSize, $blocSize = null)
    {
        $this->close();
        $this->setSegmentSize($segmentSize);
        if($blocSize) {
            $this->setBlocSize($blocSize);
        }
    }

    /**
     * Memory alloc
     */
    public function alloc()
    {
        if(null !== $this->memory) {
            return;
        }
        $this->memory = shmop_open(ftok(__FILE__, $this->identifier), "c", 0644, $this->segmentSize);
    }

    /**
     * Read datas with $uid key
     * @param mixed $uid
     * @return mixed
     */
    public function read($uid)
    {
        if(!is_int($uid) && !is_numeric($uid)) {
            throw new Exception\RuntimeException('Segment type key must integer or numeric.');
        }
        if($uid*$this->blocSize >= $this->segmentSize) {
            throw new Exception\RuntimeException('Invalid access bloc. Only ' . floor($this->segmentSize/$this->blocSize) . ' blocs are allowed.');
        }
        $this->alloc();
        $str = shmop_read($this->memory, $uid*$this->blocSize, $this->blocSize);
        return trim($str);
    }

    /**
     * Write datas on $uid key
     * @param mixed $uid
     * @param mixed $mixed
     */
    public function write($uid, $mixed)
    {
        if(!is_int($uid) && !is_numeric($uid)) {
            throw new Exception\RuntimeException('Segment type key must integer or numeric.');
        }
        if(is_object($mixed) && method_exists($mixed, '__toString')) {
            $mixed = $mixed->__toString();
        }
        if(is_int($mixed) || is_float($mixed) || is_bool($mixed)) {
            $mixed = (string)$mixed;
        }
        if(!is_string($mixed)) {
            $mixed = '';
        }
        if($uid*$this->blocSize >= $this->segmentSize) {
            throw new Exception\RuntimeException('Invalid access bloc. Only ' . floor($this->segmentSize/$this->blocSize) . ' blocs are allowed.');
        }
        $this->alloc();
        $limit = $this->getBlocSize();
        $str = mb_substr($mixed, 0, $limit);
        $str = str_pad($str, $this->blocSize);
        return shmop_write($this->memory, $str, $uid*$this->blocSize);
    }

    /**
     * Close segment
     * @param int
     */
    public function close()
    {
        if(null === $this->memory) {
            return;
        }
        shmop_close($this->memory);
        $this->memory = null;
    }

    /**
     * Get segment memory
     * @return type
     */
    public function getSegment()
    {
        return $this->memory;
    }

    /**
     * Get bloc size
     * @return int
     */
    public function getBlocSize()
    {
        return $this->blocSize;
    }

    /**
     * Set bloc size
     * @param int
     */
    public function setBlocSize($size)
    {
        if(null !== $this->memory) {
            throw new Exception\RuntimeException(
                'You can not change the segment size because memory is already allocated.'
                . ' Use realloc() function to create new memory segment.'
            );
        }
        $this->blocSize = (integer)$size;
        return $this;
    }

    /**
     * Get segment size
     * @return int
     */
    public function getSegmentSize()
    {
        return $this->segmentSize;
    }

    /**
     * Set segment size
     * @param int
     */
    public function setSegmentSize($size)
    {
        if(null !== $this->memory) {
            throw new Exception\RuntimeException(
                'You can not change the segment size because memory is already allocated.'
                . ' Use realloc() function to create new memory segment.'
            );
        }
        $this->segmentSize = (integer)$size;
        return $this;
    }

    /**
     * Get max bloc allow
     */
    public function canAllowBlocsMemory($numBloc)
    {
        return floor($this->segmentSize/$this->blocSize) >= $numBloc;
    }
}
