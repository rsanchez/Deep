<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\AbstractHydrator;
use DateTime;

class DateHydrator extends AbstractHydrator
{
    public function hydrate(Collection $collection, Entry $entry)
    {
        // loop through all file fields
        $entry->channel->fieldsByType('date')->each(function ($field) use ($entry) {

            $date = $entry->getAttribute('field_id_'.$field->field_id);

            $entry->setAttribute($field->field_name, $date ? DateTime::createFromFormat('U', $date) : null);

        });

        // loop through all matrix fields
        $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $field) {

                $cols = $collection->getMatrixCols()->filter(function ($col) use ($field) {
                    return $col->field_id === $field->field_id && $col->col_type === 'file';
                });

                $cols->each(function ($col) use ($row) {
                    $date = $row->getAttribute('col_id_'.$col->col_id);

                    $row->setAttribute($col->col_name, $date ? DateTime::createFromFormat('U', $date) : null);
                });

            });

        });

        // loop through all grid fields
        $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $field) {

                $cols = $collection->getGridCols()->filter(function ($col) use ($field) {
                    return $col->field_id === $field->field_id && $col->col_type === 'file';
                });

                $cols->each(function ($col) use ($row) {
                    $date = $row->getAttribute('col_id_'.$col->col_id);

                    $row->setAttribute($col->col_name, $date ? DateTime::createFromFormat('U', $date) : null);
                });

            });

        });
    }
}
