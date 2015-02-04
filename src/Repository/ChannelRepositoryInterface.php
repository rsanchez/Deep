<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

/**
 * Repository of all Channels
 */
interface ChannelRepositoryInterface
{
    /**
     * Find a Channel by IDs
     * @var int $id
     * @return \rsanchez\Deep\Model\Channel|null
     */
    public function find($id);

    /**
     * Get Collection of Channels by channel ID
     *
     * @var array $channelIds
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function getChannelsById(array $channelIds);
    /**
     * Get Collection of Channels by channel name
     *
     * @var array $channelNames
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function getChannelsByName(array $channelNames);

    /**
     * Get single Channel by channel ID
     *
     * @var int $channelId
     * @return \rsanchez\Deep\Model\Channel|null
     */
    public function getChannelById($channelId);

    /**
     * Get single Channel by channel name
     *
     * @var string $channelName
     * @return \rsanchez\Deep\Model\Channel|null
     */
    public function getChannelByName($channelName);

    /**
     * Get the Channel model
     * @return \rsanchez\Deep\Model\Channel
     */
    public function getModel();

    /**
     * Get the Collection of all items
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function getCollection();
}
