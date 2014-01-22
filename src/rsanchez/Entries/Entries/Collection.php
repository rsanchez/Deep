<?php

namespace rsanchez\Entries\Entries;

use \Iterator;
use rsanchez\Entries\Entries\Filter;

class Collection implements Iterator {

	public $total_results = 0;
	public $count = 1;

	protected $entries = array();

    public function push(Entry $entry)
    {
        array_push($this->entries, $entry);
        $this->total_results++;
    }
  
  public function rewind()
	{
		$this->count = 1;
  }

  public function current()
	{
		return $this->entries[$this->count - 1];
  }

  public function key()
	{
    return $this->count - 1;
  }

  public function next() {
    ++$this->count;
  }

  public function valid()
	{
    return array_key_exists($this->count - 1, $this->entries);
  }
}

