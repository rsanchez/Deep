<?php

namespace rsanchez\Entries\Entry;

use rsanchez\Entries\Entry;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Channel;
use rsanchez\Entries\Entry\Field\Factory as FieldFactory;
use \stdClass;

class Factory
{
    public function createEntry(Entries $entries, Channel $channel, FieldFactory $fieldFactory, stdClass $row)
    {
        return new Entry($entries, $channel, $fieldFactory, $row);
    }
}
