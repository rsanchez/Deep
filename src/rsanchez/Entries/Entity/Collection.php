<?php

namespace rsanchez\Entries\Entity;

use rsanchez\Entries\Entity\Entity;
use Iterator;

class Collection implements Iterator
{
    public $total_results = 0;
    public $count = 1;

    protected $entities = array();

    public function push(Entity $entity)
    {
        array_push($this->entities, $entity);
        $this->total_results++;
    }

    public function rewind()
    {
        $this->count = 1;
    }

    public function current()
    {
        return $this->entities[$this->count - 1];
    }

    public function key()
    {
        return $this->count - 1;
    }

    public function next()
    {
        ++$this->count;
    }

    public function valid()
    {
        return array_key_exists($this->count - 1, $this->entities);
    }
}
