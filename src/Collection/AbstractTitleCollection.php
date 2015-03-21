<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Title;
use rsanchez\Deep\Repository\ChannelRepository;

/**
 * Collection of \rsanchez\Deep\Model\Title
 */
abstract class AbstractTitleCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * All of the entry IDs in this collection (including related entries)
     * @var array
     */
    protected $entryIds = array();

    /**
     * Channels used by this collection
     * @var \rsanchez\Deep\Collection\ChannelCollection
     */
    protected $channels;

    /**
     * Instantiate a collection of models
     * @param  array                                             $models
     * @param  \rsanchez\Deep\Repository\ChannelRepository       $channelRepository
     * @return \rsanchez\Deep\Collection\AbstractTitleCollection
     */
    public static function create(array $models, ChannelRepository $channelRepository)
    {
        $collection = new static($models);

        $collection->setChannelRepository($channelRepository);

        $channelIds = array();

        foreach ($models as $model) {
            $collection->entryIds[] = $model->entry_id;

            if (! in_array($model->channel_id, $channelIds)) {
                $channelIds[] = $model->channel_id;
            }
        }

        $collection->setChannels($channelRepository->getChannelsById($channelIds));

        return $collection;
    }

    /**
     * Create a new collection using the current instance's properties
     * as injected dependencies.
     *
     * Useful when making a collection of a subset of this collection
     * @param  array                                             $models
     * @return \rsanchez\Deep\Collection\AbstractTitleCollection
     */
    public function createChildCollection(array $models)
    {
        return self::create($models, $this->channelRepository);
    }

    /**
     * Set the Channel Repository
     * @param  \rsanchez\Deep\Repository\ChannelRepository $channelRepository
     * @return void
     */
    public function setChannelRepository(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * Set the channels used by this collection
     * @param  \rsanchez\Deep\Collection\ChannelCollection $channels
     * @return void
     */
    public function setChannels(ChannelCollection $channels)
    {
        $this->channels = $channels;
    }

    /**
     * Get the channels used by this collection
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Get all the entry Ids from this collection.
     * This includes both the entries directly in this collection,
     * and entries found in Playa/Relationship fields
     *
     * @return array
     */
    public function getEntryIds()
    {
        return $this->entryIds;
    }

    /**
     * Add an additional entry id to this collection
     *
     * @param  string|int $entryId
     * @return void
     */
    public function addEntryId($entryId)
    {
        if (! in_array($entryId, $this->entryIds)) {
            $this->entryIds[$entryId] = $entryId;
        }
    }

    /**
     * Add additional entry ids to this collection
     *
     * @param  array $entryIds
     * @return void
     */
    public function addEntryIds(array $entryIds)
    {
        foreach ($entryIds as $entryId) {
            $this->addEntryId($entryId);
        }
    }

    /**
     * Whether or not this collection supports custom fields
     *
     * @return bool
     */
    public function hasCustomFields()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        if (func_num_args() === 0) {
            $options = JSON_NUMERIC_CHECK;
        }

        return parent::toJson($options);
    }
}
