<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\FilePaths;
use rsanchez\Entries\Channel;
use rsanchez\Entries\Entry\Field;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entry;
use \Pimple;

class Factory extends Pimple
{
    protected $classMap = array(
        'date' => '\rsanchez\Entries\Entry\Field\Date',
        /*
        'file' => '\rsanchez\Entries\Entry\Field\File',
        'matrix' => '\rsanchez\Entries\Entry\Field\Matrix',
        'grid' => '\rsanchez\Entries\Entry\Field\Grid',
        'playa' => '\rsanchez\Entries\Entry\Field\Playa',
        'relationships' => '\rsanchez\Entries\Entry\Field\Relationships',
        'assets' => '\rsanchez\Entries\Entry\Field\Assets',
        */
    );

    public function __construct(FilePaths $filePaths)
    {
        parent::__construct();

        $this['filePaths'] = $filePaths;

        $this['date'] = $this->factory(function ($container) {
            return new Date($container['channel'], $container['channelField'], $container['entry'], $container['value']);
        });

        $this['file'] = $this->factory(function ($container) {
            return new File($container['channel'], $container['channelField'], $container['entry'], $container['value'], $container['filePaths']);
        });

        $this['field'] = $this->factory(function ($container) {
            return new Field($container['channel'], $container['channelField'], $container['entry'], $container['value']);
        });
    }

    public function createField(Channel $channel, ChannelField $channelField, Entry $entry, $value)
    {
        $this['channel'] = $channel;
        $this['channelField'] = $channelField;
        $this['entry'] = $entry;
        $this['value'] = $value;

        if (isset($this[$channelField->field_type])) {
            return $this[$channelField->field_type];
        }

        return $this['field'];
    }
}
