<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\HydratorInterface;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;

class MatrixHydrator implements HydratorInterface
{
    public function hydrateCollection(Collection $collection)
    {
        if ($collection->isEmpty()) {
            return;
        }

        $entryIds = $collection->modelKeys();

        $fields = $collection->fetch('channel.fields');

        $fieldIds = array_get($fields, 'field_id');

        $cols = MatrixCol::fieldId($fieldIds)->get();

        $rows = MatrixRow::entryId($entryIds)->get();

        $collection->each(function ($entry) use ($cols, $rows) {

            $entry->channel->fields->filter(function ($field) {

                return $field->field_type === 'matrix';

            })->each(function ($field) use ($entry, $cols, $rows) {

                // get the cols associated with thsi field
                $fieldCols = $cols->filter(function ($col) use ($field) {
                    return $col->field_id === $field->field_id;
                });

                $entry->setAttribute($field->field_name, $rows->filter(function ($row) use ($entry, $field) {
                    return $entry->getKey() === $row->getKey() && $field->field_id === $row->field_id;
                }));

            });

        });
    }
}
