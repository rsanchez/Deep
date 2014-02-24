<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\PlayaEntry;

class PlayaHydrator extends AbstractHydrator
{
    public function __construct(Collection $collection)
    {
        $this->entries = PlayaEntry::parentEntryId($collection->modelKeys())->get();

        $collection->addEntryIds($this->entries->modelKeys());
    }

    public function hydrate(Collection $collection)
    {
        $relatedEntries = $this->entries;

        $collection->each(function ($entry) use ($collection, $relatedEntries) {

            // loop through all playa fields
            $entry->channel->fieldsByType('playa')->each(function ($field) use ($entry, $relatedEntries) {

                $entry->setAttribute($field->field_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field) {
                    return $entry->getKey() === $relatedEntry->parent_entry_id && $field->field_id === $relatedEntry->parent_field_id;
                }));

            });

            // loop through all matrix fields
            $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry, $relatedEntries) {

                $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $relatedEntries, $field) {

                    $cols = $collection->getMatrixCols()->filter(function ($col) use ($field) {
                        return $col->field_id === $field->field_id && $col->col_type === 'playa';
                    });

                    $cols->each(function ($col) use ($entry, $field, $row, $relatedEntries) {
                        $row->setAttribute($col->col_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field, $row, $col) {
                            return $entry->getKey() === $relatedEntry->parent_entry_id && $field->field_id === $relatedEntry->parent_field_id && $col->col_id === $relatedEntry->parent_col_id;
                        }));
                    });

                });

            });

        });
    }
}
