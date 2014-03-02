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
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $this->entries = RelationshipEntry::parentEntryId($collection->modelKeys())->get();

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

        // loop through all relationship fields
        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $relatedEntries) {

            $entry->setAttribute($field->field_name, $relatedEntries->filter(function ($relatedEntry) use ($entry, $field) {
                return $entry->getKey() === $relatedEntry->parent_id && $field->field_id === $relatedEntry->field_id;
            }));

        });

        // loop through all grid fields
        $entry->channel->fieldsByType('grid')->each(function ($field) use ($collection, $entry, $relatedEntries, $fieldtype) {

            $entry->getAttribute($field->field_name)->each(function ($row) use ($collection, $entry, $relatedEntries, $field, $fieldtype) {

                $cols = $collection->getGridCols()->filter(function ($col) use ($field, $fieldtype) {
                    return $col->field_id === $field->field_id && $col->col_type === $fieldtype;
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
