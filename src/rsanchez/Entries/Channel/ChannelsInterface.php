<?php

namespace rsanchez\Entries\Channel;

use Iterator;

interface ChannelsInterface extends Iterator
{
    public function find($id);
}
