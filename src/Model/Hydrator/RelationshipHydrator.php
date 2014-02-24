<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\RelationshipEntry;

class RelationshipHydrator extends AbstractHydrator
{
    public function preload(Collection $collection)
    {
        $entries = RelationshipEntry::parentEntryId($collection->modelKeys())->get();

        $collection->setRelationshipEntries($entries);
    }

    public function hydrate(Collection $collection)
    {
        $relatedEntries = RelationshipEntry::parentEntryId($collection->modelKeys())->get();

        $collection->each(function ($entry) use ($collection, $relatedEntries) {

            // loop through all relationship fields
            $entry->channel->fieldsByType('relationship')->each(function ($field) use ($entry, $relatedEntries) {

                $entry->setAttribute($field->field_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field) {
                    return $entry->getKey() === $relatedEntry->parent_id && $field->field_id === $relatedEntry->field_id;
                }));

            });

            // loop through all grid fields
            $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry, $relatedEntries) {

                $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $relatedEntries, $field) {

                    $cols = $collection->getGridCols()->filter(function ($col) use ($field) {
                        return $col->field_id === $field->field_id;
                    });

                    $cols->each(function ($col) use ($entry, $field, $row, $relatedEntries) {
                        $row->setAttribute($col->col_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field, $row, $col) {
                            return $entry->getKey() === $relatedEntry->parent_id && $col->col_id === $relatedEntry->grid_col_id;
                        }));
                    });

                });

            });

        });
    }
}
