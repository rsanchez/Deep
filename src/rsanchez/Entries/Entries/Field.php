<?php

namespace rsanchez\Entries\Entries;

use \rsanchez\Entries\Channel;
use \rsanchez\Entries\Channel\Field as ChannelField;
use \rsanchez\Entries\Entries\Entry;

class Field
{
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
        return $this->value;
    }

    public function __get($name)
    {
        return $this->channelField->$name;
    }

/*
    //tag modifiers
    public function __call($name, $args) {
        $params = isset($args[0]) ? $args[0] : array();
        return $this->ee->call_fieldtype($this->channel, $this->entry, $this, $params, $name);
    }

    public function __invoke($params = array()) {
        return $this->__call('tag', array($params));
    }
    */
}
