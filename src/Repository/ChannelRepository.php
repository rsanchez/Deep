<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\ChannelCollection;
use rsanchez\Deep\Model\Channel;

/**
 * Repository of all Channels
 */
class ChannelRepository extends AbstractRepository implements ChannelRepositoryInterface
{
    /**
     * Array of Channels keyed by channel_id
     * @var array
     */
    protected $channelsById = [];

    /**
     * Array of Channels keyed by channel_name
     * @var array
     */
    protected $channelsByName = [];

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\Channel $model
     */
    public function __construct(Channel $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            parent::loadCollection();

            foreach ($this->collection as $channel) {
                $this->channelsById[$channel->channel_id] = $channel;
                $this->channelsByName[$channel->channel_name] = $channel;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelsById(array $channelIds)
    {
        if (empty($channelIds)) {
            return new ChannelCollection();
        }

        return $this->getCollection()->filter(function ($channel) use ($channelIds) {
            return in_array($channel->channel_id, $channelIds);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelsByName(array $channelNames)
    {
        if (empty($channelNames)) {
            return new ChannelCollection();
        }

        return $this->getCollection()->filter(function ($channel) use ($channelNames) {
            return in_array($channel->channel_name, $channelNames);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getChannelById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelById($channelId)
    {
        $this->loadCollection();

        return array_key_exists($channelId, $this->channelsById) ? $this->channelsById[$channelId] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelByName($channelName)
    {
        $this->loadCollection();

        return array_key_exists($channelName, $this->channelsByName) ? $this->channelsByName[$channelName] : null;
    }
}
