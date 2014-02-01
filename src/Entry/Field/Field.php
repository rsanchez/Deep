<?php

namespace rsanchez\Deep\Entry\Field;

use rsanchez\Deep\Entity\Field\Field as EntityField;
use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Channel\Field\Field as ChannelField;
use rsanchez\Deep\Entry\Field\Factory as EntryFieldFactory;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Entry\Collection as EntryCollection;

class Field extends EntityField
{
    protected $entry;

    public function __construct(
        $value,
        ChannelField $channelField,
        Entries $entries,
        $entry = null
    ) {
        parent::__construct($value, $channelField, $entries, $entry);

        $this->channelField = $this->property;
        $this->entry = $this->entity;
        $this->entries = $this->collection;
    }

    public function setEntity(Entry $entry)
    {
        $this->entity = $entry;
    }

    public function setEntry(Entry $entry)
    {
        $this->setEntity($entry);
    }
}
