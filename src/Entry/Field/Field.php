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
    public function __construct($value, ChannelField $channelField)
    {
        parent::__construct($value, $channelField);
    }
}
