<?php

namespace rsanchez\Entries;

use \Iterator;

interface ChannelsInterface implements Iterator {
	public function find($id);
}
