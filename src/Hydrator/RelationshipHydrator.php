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
use rsanchez\Deep\Model\RelationshipEntry;

/**
 * Hydrator for the Relationship fieldtype
 */
class RelationshipHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection)
    {
        parent::__construct($collection);

        $this->entries = RelationshipEntry::parentEntryId($collection->modelKeys())->get();

        $collection->addEntryIds($this->entries->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $collection = $this->collection;
        $relatedEntries = $this->entries;

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
                        return $entry->getKey() === $relatedEntry->parent_id && $relatedEntry->field_id === $field->field_id && $col->col_id === $relatedEntry->grid_col_id;
                    }));
                });

            });

        });
    }
}
