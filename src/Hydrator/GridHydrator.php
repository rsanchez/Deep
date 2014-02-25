<?php

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;

class GridHydrator extends AbstractHydrator
{
    protected $rows = array();

    public function __construct(Collection $collection)
    {
        $fieldIds = $collection->getFieldIdsByFieldtype('grid');

        $gridCols = GridCol::fieldId($fieldIds)->get();

        $collection->setGridCols($gridCols);
    }

    public function preload(Collection $collection)
    {
        $entryIds = $collection->entryIds();

        $fieldIds = $collection->getFieldIdsByFieldtype('grid');

        foreach ($fieldIds as $fieldId) {
            $this->rows[$fieldId] = GridRow::fieldId($fieldId)->entryId($entryIds)->get();
        }
    }

    public function hydrate(Collection $collection, Entry $entry)
    {
        $rowsByFieldId = $this->rows;

        $entry->channel->fieldsByType('grid')->each(function ($field) use ($entry, $rowsByFieldId, $collection) {

            $rows = $rowsByFieldId[$field->field_id];

            $cols = $collection->getGridCols()->filter(function ($col) use ($field) {
                return $col->field_id === $field->field_id;
            });

            $fieldRows = $rows->filter(function ($row) use ($entry) {
                return $entry->getKey() === $row->getKey();
            });

            $cols->each(function ($col) use ($fieldRows) {
                $fieldRows->each(function ($row) use ($col) {
                    $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
                });
            });

            $entry->setAttribute($field->field_name, $fieldRows);

        });
    }
}
