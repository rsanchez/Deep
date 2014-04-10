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
use rsanchez\Deep\Collection\TitleCollection;
use rsanchez\Deep\Model\Field;

/**
 * Collection of \rsanchez\Deep\Model\Entry
 */
class EntryCollection extends TitleCollection
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
     * Fields used by this collection
     * @var \rsanchez\Deep\Collection\FieldCollection
     */
    public $fields;

    /**
     * Get a list of names of fieldtypes used by this  collection
     * @return array
     */
    public function getFieldtypes()
    {
        return $this->fieldtypes;
    }

    /**
     * Register a field used by this collection
     * @param  \rsanchez\Deep\Model\Field $field
     * @return void
     */
    public function addField(Field $field)
    {
        $this->fields->push($field);

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
