<?php

namespace rsanchez\Entries\Entry;

use \rsanchez\Entries\Channel;
use \rsanchez\Entries\Channel\Field as ChannelField;
use \rsanchez\Entries\Entry;

class Field
{
    protected $channel;
    protected $channelField;
    protected $entry;
    public $value;

    public function __construct(Channel $channel, ChannelField $channelField, Entry $entry, $value)
    {
        $this->channel = $channel;
        $this->channelField = $channelField;
        $this->entry = $entry;
        $this->value = $value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function __invoke()
    {
        return $this->__toString();
    }
}
