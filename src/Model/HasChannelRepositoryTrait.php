<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\ChannelRepositoryInterface;

/**
 * Trait for possessing a ChannelRepository
 */
trait HasChannelRepositoryTrait
{
    /**
     * Global Channel Repository
     * @var \rsanchez\Deep\Repository\ChannelRepositoryInterface
     */
    protected static $channelRepository;

    /**
     * Set the global ChannelRepository
     * @param  \rsanchez\Deep\Repository\ChannelRepositoryInterface $channelRepository
     * @return void
     */
    public static function setChannelRepository(ChannelRepositoryInterface $channelRepository)
    {
        static::$channelRepository = $channelRepository;
    }

    /**
     * Unset the global ChannelRepository
     * @return void
     */
    public static function unsetChannelRepository()
    {
        static::$channelRepository = null;
    }

    /**
     * Get the global ChannelRepository
     * @return \rsanchez\Deep\Repository\ChannelRepositoryInterface
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
