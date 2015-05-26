<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * {@inheritdoc}
 *
 * Joins with playa_relationships table
 */
class PlayaEntry extends Entry
{
    /**
     * {@inheritdoc}
     */
    protected $hidden = ['channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled', 'parent_entry_id', 'parent_field_id', 'parent_col_id', 'parent_row_id', 'parent_var_id', 'parent_is_draft', 'child_entry_id', 'rel_order', 'rel_id'];

    /**
     * {@inheritdoc}
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\PlayaCollection';

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return array_merge(parent::joinTables(), [
            'playa_relationships' => function ($query) {
                $query->join('playa_relationships', 'playa_relationships.child_entry_id', '=', 'channel_titles.entry_id');
            },
        ]);
    }

    /**
     * Filter by Parent Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : [$entryId];

        return $this->requireTable($query, 'playa_relationships', true)->whereIn('playa_relationships.parent_entry_id', $entryId);
    }
}
