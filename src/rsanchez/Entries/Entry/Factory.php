<?php

namespace rsanchez\Entries\Entry;

use rsanchez\Entries\Entries;
use rsanchez\Entries\Channel\Channel;
use rsanchez\Entries\Entry;
use rsanchez\Entries\Entity\Factory as EntityFactory;
use \stdClass;

class Factory extends EntityFactory
{
    public function createEntry(stdClass $row, Entries $entries, Channel $channel)
    {
        $fieldCollection = $this->fieldCollectionFactory->createCollection();

        foreach ($channel->fields as $channelField) {
            $property = 'field_id_'.$channelField->id();
            $value = property_exists($row, $property) ? $row->$property : '';
            $field = $this->fieldFactory->createField($value, $channelField, $entries);
            $fieldCollection->push($field);
        }

        return new Entry($row, $fieldCollection, $entries, $channel);
    }
}
