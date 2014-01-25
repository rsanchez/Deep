<?php

namespace rsanchez\Entries\Entries\Field;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entries\Field;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entries\Entry;

class Factory
{
    public function createField(Channel $channel, ChannelField $channelField, Entry $entry, $value)
    {
        //@TODO map other fieldtypes
        
        return new Field($channel, $channelField, $entry, $value);
    }
}
