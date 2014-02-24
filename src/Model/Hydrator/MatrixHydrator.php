<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;

class MatrixHydrator extends AbstractHydrator
{
    public function __construct(Collection $collection)
    {
        $fieldIds = $collection->getFieldIdsByFieldtype('matrix');

        $matrixCols = MatrixCol::fieldId($fieldIds)->get();

        $collection->setMatrixCols($matrixCols);
    }

    public function hydrate(Collection $collection)
    {
        $entryIds = $collection->modelKeys();

        $rows = MatrixRow::entryId($entryIds)->get();

        $collection->each(function ($entry) use ($rows, $collection) {

            $entry->channel->fieldsByType('matrix')->each(function ($field) use ($entry, $rows, $collection) {

                $cols = $collection->getMatrixCols()->filter(function ($col) use ($field) {
                    return $col->field_id === $field->field_id;
                });

                $fieldRows = $rows->filter(function ($row) use ($entry, $field) {
                    return $entry->getKey() === $row->getKey() && $field->field_id === $row->field_id;
                });

                $cols->each(function ($col) use ($fieldRows) {
                    $fieldRows->each(function ($row) use ($col) {
                        $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
                    });
                });

                $entry->setAttribute($field->field_name, $fieldRows);

            });

        });
    }
}
