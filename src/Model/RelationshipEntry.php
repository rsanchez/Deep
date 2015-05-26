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
 * Joins with relationships table
 */
class RelationshipEntry extends Entry
{
    /**
     * {@inheritdoc}
     */
    protected $hidden = ['channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled', 'parent_id', 'field_id', 'grid_field_id', 'grid_col_id', 'grid_row_id', 'child_id', 'order', 'relationship_id'];

    /**
     * {@inheritdoc}
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\RelationshipCollection';

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return array_merge(parent::joinTables(), [
            'relationships' => function ($query) {
                $query->join('relationships', 'relationships.child_id', '=', 'channel_titles.entry_id');
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

        return $this->requireTable($query, 'relationships', true)->whereIn('relationships.parent_id', $entryId);
    }

    /**
     * Get the parents of the specified entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : [$entryId];

        return $query->join('relationships', 'relationships.parent_id', '=', 'channel_titles.entry_id')
            ->whereIn('relationships.child_id', $entryId)
            ->orderBy('relationships.order', 'asc')
            ->groupBy('relationships.child_id')
            ->groupBy('relationships.field_id')
            ->groupBy('channel_titles.entry_id');
    }

    /**
     * Get the siblings of the specified entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSiblings(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : [$entryId];

        $connection = $query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        return $query->join('relationships', 'relationships.child_id', '=', 'channel_titles.entry_id')
            ->join($connection->raw("`{$tablePrefix}relationships` AS `{$tablePrefix}relationships_2`"), 'relationships_2.parent_id', '=', 'relationships.parent_id')
            ->addSelect('*')
            ->addSelect('relationships_2.child_id AS sibling_id')
            ->whereIn('relationships_2.child_id', $entryId)
            ->orderBy('relationships.order', 'asc')
            ->groupBy('relationships_2.child_id')
            ->groupBy('relationships.field_id')
            ->groupBy('channel_titles.entry_id');
    }
}
