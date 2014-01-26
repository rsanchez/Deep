<?php

namespace rsanchez\Entries\Entry;

use rsanchez\Entries\Entry;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Channel;
use rsanchez\Entries\Entry\Field\Factory as FieldFactory;
use rsanchez\Entries\Entry\Field\CollectionFactory as FieldCollectionFactory;
use \stdClass;

class Factory
{
    protected $fieldFactory;
    protected $fieldCollectionFactory;

    public function __construct(FieldFactory $fieldFactory, FieldCollectionFactory $fieldCollectionFactory)
    {
        $this->fieldFactory = $fieldFactory;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
    }

    public function createEntry(stdClass $row, Entries $entries, Channel $channel)
    {
        $fieldCollection = $this->fieldCollectionFactory->createCollection();

        foreach ($channel->fields as $channelField) {
            $property = 'field_id_'.$channelField->field_id;
            $value = property_exists($row, $property) ? $row->$property : '';
            $field = $this->fieldFactory->createField($value, $channel, $channelField, $entries);
            $fieldCollection->push($field);
        }

        return new Entry($row, $fieldCollection, $entries, $channel);
    }
}
