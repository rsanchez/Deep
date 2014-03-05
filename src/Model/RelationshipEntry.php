<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\Entry;

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
    protected $hidden = array('channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled', 'parent_id', 'field_id', 'grid_field_id', 'grid_col_id', 'grid_row_id', 'child_id', 'order', 'relationship_id');

    /**
     * Join tables
     * @var array
     */
    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
        'relationships' => array('relationships.child_id', 'channel_titles.entry_id'),
    );

    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\EntryCollection';

    /**
     * Filter by Parent Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'relationships', true)->whereIn('relationships.parent_id', $entryId);
    }
}
