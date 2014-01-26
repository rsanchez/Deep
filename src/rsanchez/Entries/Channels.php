<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Channel\Storage as ChannelStorage;
use rsanchez\Entries\Channel\Field\Group as FieldGroup;
use rsanchez\Entries\Channel\Fields;
use rsanchez\Entries\Channel\Field\GroupFactory as FieldGroupFactory;
use rsanchez\Entries\Channel\Factory as ChannelFactory;
use \IteratorAggregate;

class Channels implements IteratorAggregate
{
    public $fields;
    private $channels = array();

    public function __construct(
        ChannelStorage $storage,
        Fields $fields,
        ChannelFactory $factory,
        FieldGroupFactory $fieldGroupFactory
    ) {
        $this->fields = $fields;

        foreach ($storage() as $channelRow) {

            // provide an empty fieldGroup if one isn't found
            if (!$channelRow->field_group || ! $fieldGroup = $fields->findGroup($channelRow->field_group)) {
                $fieldGroup = $fieldGroupFactory->createGroup(0);
            }

            $channel = $factory->createChannel($fieldGroup, $channelRow);

            $this->push($channel);
        }
    }

    public function push(Channel $channel)
    {
        array_push($this->channels, $channel);
        $this->channelsById[$channel->channel_id] =& $channel;
        $this->channelsByName[$channel->channel_name] =& $channel;
    }

    public function find($id)
    {
        if (is_numeric($id)) {
            //@TODO custom exception
            if (! array_key_exists($id, $this->channelsById)) {
                throw new \Exception('invalid channel id');
            }

            return $this->channelsById[$id];
        }

        if (! array_key_exists($id, $this->channelsByName)) {
            throw new \Exception('invalid channel name');
        }

        return $this->channelsByName[$id];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->channels);
    }
}
