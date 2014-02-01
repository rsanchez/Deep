<?php

namespace rsanchez\Deep\Entry\Field;

use rsanchez\Deep\Entry\Field\Field;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Channel\Field\Field as ChannelField;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entity\Field\Factory as EntityFieldFactory;

class Factory extends EntityFieldFactory
{
    public function createField($value, ChannelField $channelField)
    {
        return new Field($value, $channelField);
    }
}
