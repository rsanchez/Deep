<?php

namespace rsanchez\Entries\Entity\Field;

use rsanchez\Entries\FilePaths;
use rsanchez\Entries\Channel;
use rsanchez\Entries\Entity\Field;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Entries\Entity;
use \Pimple;

class Factory extends Pimple
{
    public function __construct(FilePaths $filePaths, ChannelFieldFactory $channelFieldFactory)
    {
        parent::__construct();

        $this['filePaths'] = $filePaths;
        $this['channelFieldFactory'] = $channelFieldFactory;

        $this['date'] = $this->factory(function ($container) {
            return new Date($container['value'], $container['channelField'], $container['entries'], $container['entry']);
        });

        $this['file'] = $this->factory(function ($container) {
            return new File($container['value'], $container['channelField'], $container['entries'], $container['entry'], $container['filePaths']);
        });

        $this['matrix'] = $this->factory(function ($container) {
            return new Matrix($container['value'], $container['channelField'], $container['entries'], $container['entry'], $container, $container['channelFieldFactory']);
        });

        $this['field'] = $this->factory(function ($container) {
            return new Field($container['value'], $container['channelField'], $container['entries'], $container['entry']);
        });
    }

    public function createField(
        $value,
        ChannelField $channelField,
        Entries $entries,
        $entry = null
    ) {
        $this['channelField'] = $channelField;
        $this['entries'] = $entries;
        $this['entry'] = $entry;
        $this['value'] = $value;

        if (isset($this[$channelField->type()])) {
            return $this[$channelField->type()];
        }

        return $this['field'];
    }
}
