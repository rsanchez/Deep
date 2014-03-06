<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Collection\MatrixColCollection;
use rsanchez\Deep\Collection\GridColCollection;
use rsanchez\Deep\Model\Field;

/**
 * Collection of \rsanchez\Deep\Model\Entry
 */
class EntryCollection extends Collection
{
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
     * All of the entry IDs in this collection (including related entries)
     * @var array
     */
    protected $entryIds = array();

    /**
     * Fieldtypes used by this collection
     * @var array
     */
    protected $fieldtypes = array();

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldIdsByFieldtype = array();

    /**
     * Channels used by this collection
     * @var \rsanchez\Deep\Collection\ChannelCollection
     */
    public $channels;

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
     * Add additional entry ids to this collection
     *
     * @param array $entryIds
     */
    public function addEntryIds(array $entryIds)
    {
        $this->entryIds = array_unique(array_merge($this->entryIds, $entryIds));
    }

    public function getFieldtypes()
    {
        return $this->fieldtypes;
    }

    public function addField(Field $field)
    {
        if (! in_array($field->field_type, $this->fieldtypes)) {
            $this->fieldtypes[] = $field->field_type;
        }

        $this->fieldIdsByFieldtype[$field->field_type][] = $field->field_id;
    }

    /**
     * Check if this collection uses the specified fieldtype
     *
     * @param  string  $fieldtype
     * @return boolean
     */
    public function hasFieldtype($fieldtype)
    {
        return in_array($fieldtype, $this->fieldtypes);
    }

    /**
     * Get the field IDs for the specified fieldtype
     *
     * @param  string $fieldtype
     * @return array
     */
    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldIdsByFieldtype[$fieldtype]) ? $this->fieldIdsByFieldtype[$fieldtype] : array();
    }

    /**
     * Set the Matrix columns for this collection
     *
     * @param  \rsanchez\Deep\Collection\MatrixColCollection $matrixCols
     * @return void
     */
    public function setMatrixCols(MatrixColCollection $matrixCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $matrixCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
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
     * Set the Grid columns for this collection
     *
     * @param  \rsanchez\Deep\Collection\GridColCollection $gridCols
     * @return void
     */
    public function setGridCols(GridColCollection $gridCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $gridCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
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
