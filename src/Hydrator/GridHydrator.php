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
    public function __construct(EntryCollection $collection)
    {
        parent::__construct($collection);

        $fieldIds = $collection->getFieldIdsByFieldtype('grid');

        $this->cols = GridCol::fieldId($fieldIds)->get();

        $collection->setGridCols($this->cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $fieldIds = $this->collection->getFieldIdsByFieldtype('grid');

        foreach ($fieldIds as $fieldId) {
            $this->rows[$fieldId] = GridRow::fieldId($fieldId)->entryId($entryIds)->get();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $cols = $this->cols;
        $rowsByFieldId = $this->rows;

        $entry->channel->fieldsByType('grid')->each(function ($field) use ($entry, $rowsByFieldId, $cols) {

            $rows = $rowsByFieldId[$field->field_id];

            $fieldCols = $cols->filter(function ($col) use ($field) {
                return $col->field_id === $field->field_id;
            });

            $fieldRows = $rows->filter(function ($row) use ($entry) {
                return $entry->getKey() === $row->getKey();
            })->each(function ($row) use ($fieldCols) {
                $row->setCols($fieldCols);

                $fieldCols->each(function ($col) use ($row) {
                    $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
                });
            });

            $entry->setAttribute($field->field_name, $fieldRows);

        });
    }
}
