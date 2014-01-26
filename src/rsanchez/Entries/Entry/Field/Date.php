<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entry\Field;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entry;

class Date extends Field
{
    public function __invoke($format = 'U')
    {
        return $this->value ? date($format, $this->value) : null;
    }
}
