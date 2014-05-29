<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;

/**
 * Hydrator for the Grid fieldtype
 */
class GridHydrator extends AbstractHydrator
{
    /**
     * All Grid cols used by this collection
     * @var \rsanchez\Deep\Collection\GridColCollection
     */
    protected $cols;

    /**
     * Array of field_id => \rsanchez\Deep\Collection\GridRowCollection
     * @var array
     */
    protected $rows = array();

    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $fieldIds = $collection->getFieldIdsByFieldtype($fieldtype);

        $this->cols = GridCol::fieldId($fieldIds)->get();

        $collection->setGridCols($this->cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $fieldIds = $this->collection->getFieldIdsByFieldtype($this->fieldtype);

        foreach ($fieldIds as $fieldId) {
            $this->rows[$fieldId] = GridRow::fieldId($fieldId)->entryId($entryIds)->orderBy('row_order', 'asc')->get();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $cols = $this->cols;
        $rowsByFieldId = $this->rows;

        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $rowsByFieldId, $cols) {

            $rows = $rowsByFieldId[$field->field_id];

            $fieldCols = $cols->filter(function ($col) use ($field) {
                return $col->field_id === $field->field_id;
            });

            $fieldRows = $rows->filter(function ($row) use ($entry) {
                return $entry->getKey() === $row->entry_id;
            })->each(function ($row) use ($fieldCols) {
                $row->setCols($fieldCols);
            });

            $entry->setAttribute($field->field_name, $fieldRows);

        });
    }
}
