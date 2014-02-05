<?php

namespace rsanchez\Deep\Entry;

use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entity\Factory as EntityFactory;
use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;
use stdClass;

class Factory extends EntityFactory
{
    public function createEntity(stdClass $row, PropertyCollection $propertyCollection, Channel $channel)
    {
        return new Entry($row, $propertyCollection, $channel);
    }

    public function createEntry(stdClass $row, Channel $channel)
    {
        return $this->createEntity($row, $channel->fields, $channel);
    }
}
