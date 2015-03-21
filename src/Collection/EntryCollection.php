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
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\FieldRepository;

/**
 * Collection of \rsanchez\Deep\Model\Entry
 */
class EntryCollection extends TitleCollection
{
    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\Entry';

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
     * Instantiate a collection of models
     * @param  array                                       $models
     * @param  \rsanchez\Deep\Repository\ChannelRepository $channelRepository
     * @param  \rsanchez\Deep\Repository\FieldRepository   $fieldRepository
     * @return \rsanchez\Deep\Collection\EntryCollection
     */
    public static function createWithFields(array $models, ChannelRepository $channelRepository, FieldRepository $fieldRepository)
    {
        $collection = self::create($models, $channelRepository);

        $collection->setFieldRepository($fieldRepository);

        $channels = $collection->getChannels();

        $fields = $fieldRepository->getFieldsByChannelCollection($channels);

        $collection->setFields($fields);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function createChildCollection(array $models)
    {
        return self::createWithFields($models, $this->channelRepository, $this->fieldRepository);
    }

    /**
     * Set the Field Repository
     * @param  \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public function setFieldRepository(FieldRepository $fieldRepository)
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
     * {@inheritdoc}
     */
    public function hasCustomFields()
    {
        return true;
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
