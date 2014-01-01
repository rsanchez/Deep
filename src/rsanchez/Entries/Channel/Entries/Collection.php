<?php

namespace rsanchez\Entries\Channel\Entries;

use \Iterator;
use rsanchez\Entries\Channel\Entries\Filter;

class Collection implements Iterator {

	public $total_results;
	public $count;

	protected $entries;

	public function __construct(array $entries = array())
	{
		$this->entries = $entries;
		$this->total_results = count($this->entries);
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

