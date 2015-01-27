<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\ChannelRepository;

/**
 * Trait for possessing a ChannelRepository
 */
trait HasChannelRepositoryTrait
{
    /**
     * Global Channel Repository
     * @var \rsanchez\Deep\Repository\ChannelRepository
     */
    protected static $channelRepository;

    /**
     * Set the global ChannelRepository
     * @param  \rsanchez\Deep\Repository\ChannelRepository $channelRepository
     * @return void
     */
    public static function setChannelRepository(ChannelRepository $channelRepository)
    {
        static::$channelRepository = $channelRepository;
    }

    /**
     * Get the global ChannelRepository
     * @return \rsanchez\Deep\Repository\ChannelRepository
     * @throws \Exception
     */
    public static function getChannelRepository()
    {
        if (! isset(static::$channelRepository)) {
            throw new \Exception('The ChannelRepository is not set.');
        }

        return static::$channelRepository;
    }
}
