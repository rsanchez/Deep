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

class GridHydrator extends AbstractHydrator
{
    protected $cols;

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
            });

            $fieldCols->each(function ($col) use ($fieldRows) {
                $fieldRows->each(function ($row) use ($col) {
                    $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
                });
            });

            $entry->setAttribute($field->field_name, $fieldRows);

        });
    }
}
