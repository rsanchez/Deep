<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Entry;
use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Repository\ChannelRepositoryInterface;
use rsanchez\Deep\Repository\FieldRepositoryInterface;

/**
 * Collection of \rsanchez\Deep\Model\Entry
 */
class EntryCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\Entry';

    protected $channelRepository;

    protected $fieldRepository;

    /**
     * All of the entry IDs in this collection (including related entries)
     * @var array
     */
    protected $entryIds = [];

    /**
     * Channels used by this collection
     * @var \rsanchez\Deep\Collection\ChannelCollection
     */
    protected $channels;

    /**
     * Matrix columns used by this collection
     * @var \rsanchez\Deep\Collection\MatrixColCollection
     */
    protected $matrixCols;

    /**
     * Grid columns used by this collection
     * @var \rsanchez\Deep\Collection\GridColCollection
     */
    protected $gridCols;

    /**
     * Fields used by this collection
     * @var \rsanchez\Deep\Collection\FieldCollection
     */
    protected $fields;

    /**
     * Create a new collection using the current instance's properties
     * as injected dependencies.
     *
     * Useful when making a collection of a subset of this collection
     * @param  array                                             $models
     * @return \rsanchez\Deep\Collection\EntryCollection
     */
    public function createChildCollection(array $models)
    {
        return static::create($models, $this->channelRepository, $this->fieldRepository);
    }

    /**
     * Instantiate a collection of models
     * @param  array                                                   $models
     * @param  \rsanchez\Deep\Repository\ChannelRepositoryInterface    $channelRepository
     * @param  \rsanchez\Deep\Repository\FieldRepositoryInterface|null $fieldRepository
     * @return \rsanchez\Deep\Collection\EntryCollection
     */
    public static function create(array $models, ChannelRepositoryInterface $channelRepository, FieldRepositoryInterface $fieldRepository = null, $withFields = [])
    {
        $collection = new static($models);

        $collection->setChannelRepository($channelRepository);

        $channelIds = [];

        foreach ($models as $model) {
            $collection->entryIds[] = $model->entry_id;

            if (! in_array($model->channel_id, $channelIds)) {
                $channelIds[] = $model->channel_id;
            }
        }

        $collection->setChannels($channelRepository->getChannelsById($channelIds));

        if ($fieldRepository) {
            $collection->setFieldRepository($fieldRepository);

            $channels = $collection->getChannels();

            $fields = $fieldRepository->getFieldsByChannelCollection($channels, $withFields);

            $collection->setFields($fields);
        }

        return $collection;
    }

    /**
     * Set the Channel Repository
     * @param  \rsanchez\Deep\Repository\ChannelRepositoryInterface $channelRepository
     * @return void
     */
    public function setChannelRepository(ChannelRepositoryInterface $channelRepository)
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
        return count($this->fields) > 0;
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

    /**
     * Set the Field Repository
     * @param  \rsanchez\Deep\Repository\FieldRepositoryInterface $fieldRepository
     * @return void
     */
    public function setFieldRepository(FieldRepositoryInterface $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Get a list of names of fieldtypes used by this collection
     * @return array
     */
    public function getFieldtypes()
    {
        return $this->fields->getFieldtypes();
    }

    /**
     * Get the fields used by this collection
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get the fields used by this collection
     * @param  \rsanchez\Deep\Collection\FieldCollection
     * @return void
     */
    public function setFields(FieldCollection $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Check if this collection uses the specified fieldtype
     *
     * @param  string  $fieldtype
     * @return boolean
     */
    public function hasFieldtype($fieldtype)
    {
        return $this->fields->hasFieldtype($fieldtype);
    }

    /**
     * Get the field IDs for the specified fieldtype
     *
     * @param  string $fieldtype
     * @return array
     */
    public function getFieldIdsByFieldtype($fieldtype)
    {
        return $this->fields->getFieldIdsByFieldtype($fieldtype);
    }

    /**
     * Set the Matrix columns for this collection
     *
     * @param  \rsanchez\Deep\Collection\MatrixColCollection $matrixCols
     * @return void
     */
    public function setMatrixCols(MatrixColCollection $matrixCols)
    {
        $fields = $this->fields;

        $matrixCols->each(function ($col) use ($fields) {
            $fields->addFieldtype($col->col_type);
        });

        $this->matrixCols = $matrixCols;
    }

    /**
     * Get the Matrix columns for this collection
     *
     * @return \rsanchez\Deep\Collection\MatrixColCollection|null
     */
    public function getMatrixCols()
    {
        return $this->matrixCols;
    }

    /**
     * Set the Grid columns for this collection
     *
     * @param  \rsanchez\Deep\Collection\GridColCollection $gridCols
     * @return void
     */
    public function setGridCols(GridColCollection $gridCols)
    {
        $fields = $this->fields;

        $gridCols->each(function ($col) use (&$fields) {
            $fields->addFieldtype($col->col_type);
        });

        $this->gridCols = $gridCols;
    }

    /**
     * Get the Grid columns for this collection
     *
     * @return \rsanchez\Deep\Collection\GridColCollection|null
     */
    public function getGridCols()
    {
        return $this->gridCols;
    }
}
