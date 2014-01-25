<?php

namespace rsanchez\Entries\Entries;

use rsanchez\Entries\Entries\Entry;
use rsanchez\Entries\Channel;
use rsanchez\Entries\Entries\Field\Factory as FieldFactory;
use \stdClass;

class Factory
{
    public function createEntry(Channel $channel, FieldFactory $fieldFactory, stdClass $row)
    {
        return new Entry($channel, $fieldFactory, $row);
    }
}
