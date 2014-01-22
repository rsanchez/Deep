<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Channel\Storage as ChannelStorage;
use rsanchez\Entries\Channel\Factory as ChannelFactory;
use \IteratorAggregate;

class Channels implements IteratorAggregate
{
    private $channels = array();

    public function __construct(ChannelStorage $storage, FieldGroups $fieldGroups, ChannelFactory $factory, FieldGroupFactory $fieldGroupFactory)
    {
        foreach ($storage() as $channelRow) {

            // provide an empty fieldGroup if one isn't found
            if (!$channelRow->fieldGroup || ! $fieldGroup = $fieldGroups->find($channelRow->fieldGroup)) {
                $fieldGroup = $fieldGroupFactory();
            }

            $channel = $factory($channelRow, $fieldGroup);

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
