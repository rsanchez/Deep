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
use rsanchez\Deep\Model\PlayaEntry;

/**
 * Hydrator for the Playa fieldtype
 */
class PlayaHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $this->entries = PlayaEntry::parentEntryId($collection->modelKeys())->get();

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->entries->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $fieldtype = $this->fieldtype;
        $collection = $this->collection;
        $relatedEntries = $this->entries;

        // loop through all playa fields
        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $relatedEntries) {

            $entry->setAttribute($field->field_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field) {
                return $entry->getKey() === $relatedEntry->parent_entry_id && $field->field_id === $relatedEntry->parent_field_id;
            }));

        });

        // loop through all matrix fields
        $entry->channel->fieldsByType('matrix')->each(function ($field) use ($collection, $entry, $relatedEntries, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $relatedEntries, $field, $fieldtype) {

                $cols = $collection->getMatrixCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
                });

                $cols->each(function ($col) use ($entry, $field, $row, $relatedEntries) {
                    $row->setAttribute($col->col_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field, $row, $col) {
                        return $entry->getKey() === $relatedEntry->parent_entry_id
                            && $field->field_id === $relatedEntry->parent_field_id
                            && $col->col_id === $relatedEntry->parent_col_id
                            && $row->row_id === $relatedEntry->parent_row_id;
                    }));
                });

            });

        });
    }
}
