<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\Assets;

class AssetsHydrator extends AbstractHydrator
{
    protected $selections;

    public function preload(Collection $collection)
    {
        $this->selections = Assets::with('uploadPref')->entryId($collection->allEntryIds())->get();
    }

    public function hydrate(Collection $collection)
    {
        $selections = $this->selections;

        $collection->each(function ($entry) use ($collection, $selections) {

            // loop through all assets fields
            $entry->channel->fieldsByType('assets')->each(function ($field) use ($entry, $selections) {

                $entry->setAttribute($field->field_name, $selections->filter(function ($selection) use ($entry, $field) {
                    return $entry->getKey() === $selection->getKey() && $field->field_id === $selection->field_id;
                }));

            });

            // loop through all matrix fields
            $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry, $selections) {

                $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $selections, $field) {

                    $cols = $collection->getMatrixCols()->filter(function ($col) use ($field) {
                        return $col->field_id === $field->field_id && $col->col_type === 'assets';
                    });

                    $cols->each(function ($col) use ($entry, $field, $row, $selections) {
                        $row->setAttribute($col->col_name, $selections->filter(function ($selection) use ($entry, $field, $row, $col) {
                            return $entry->getKey() === $selection->getKey() && $col->col_id === $selection->col_id && $selection->content_type === 'matrix';
                        }));
                    });

                });

            });

            // loop through all grid fields
            $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry, $selections) {

                $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $selections, $field) {

                    $cols = $collection->getGridCols()->filter(function ($col) use ($field) {
                        return $col->field_id === $field->field_id && $col->col_type === 'assets';
                    });

                    $cols->each(function ($col) use ($entry, $field, $row, $selections) {
                        $value = $selections->filter(function ($selection) use ($entry, $field, $row, $col) {
                            return $entry->getKey() === $selection->getKey() && $col->col_id === $selection->col_id;
                        });
                        $row->setAttribute($col->col_name, $value);
                    });

                });

            });

        });
    }
}
