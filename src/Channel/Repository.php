<?php

namespace rsanchez\Deep\Channel;

use rsanchez\Deep\Channel\Storage as ChannelStorage;
use rsanchez\Deep\Channel\Field\Group as FieldGroup;
use rsanchez\Deep\Channel\Field\Repository as FieldRepository;
use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Channel\Field\GroupFactory as FieldGroupFactory;
use rsanchez\Deep\Channel\Factory as ChannelFactory;
use SplObjectStorage;

class Repository extends SplObjectStorage
{
    public $fieldRepository;

    public function __construct(
        ChannelStorage $storage,
        FieldRepository $fieldRepository,
        ChannelFactory $factory,
        FieldGroupFactory $fieldGroupFactory
    ) {
        $this->fieldRepository = $fieldRepository;

        foreach ($storage() as $channelRow) {

            // provide an empty fieldGroup if one isn't found
            if (!$channelRow->field_group || ! $fieldGroup = $fieldRepository->findGroup($channelRow->field_group)) {
                $fieldGroup = $fieldGroupFactory->createGroup(0);
            }

            $channel = $factory->createChannel($fieldGroup, $channelRow);

            $this->attach($channel);
        }
    }

    public function attach(Channel $channel)
    {
        $this->channelsById[$channel->channel_id] =& $channel;
        $this->channelsByName[$channel->channel_name] =& $channel;
        return parent::attach($channel);
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
}
