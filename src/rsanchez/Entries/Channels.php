<?php

namespace rsanchez\Entries;

use \Iterator;

class Channels implements Iterator {

	protected $index = 0;

	protected $channels = array();
	protected $channelsById = array();
	protected $channelsByName = array();

	public function __construct(array $channels = array())
	{
		$this->channels = $channels;

		foreach ($this->channels as $channel) {
			$this->channelsById[$channel->channel_id] =& $channel;
			$this->channelsByName[$channel->channel_name] =& $channel;
		}
	}

	public function find($id) {
		if (is_numeric($id)) {
			//@TODO custom exception
			if ( ! array_key_exists($id, $this->channelsById)) {
				throw new \Exception('invalid channel id');
			}

			return $this->channelsById[$id];
		}

		if ( ! array_key_exists($id, $this->channelsByName)) {
			throw new \Exception('invalid channel name');
		}

		return $this->channelsByName[$id];
	}
  
  public function rewind()
	{
		$this->index = 0;
  }

  public function current()
	{
		return $this->channels[$this->index];
  }

  public function key()
	{
    return $this->index;
  }

  public function next() {
    ++$this->index;
  }

  public function valid()
	{
    return array_key_exists($this->index, $this->channels);
  }
}

