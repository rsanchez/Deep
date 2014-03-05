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
 * Joins with playa_relationships table
 */
class PlayaEntry extends Entry
{
    /**
     * {@inheritdoc}
     */
    protected $hidden = array('channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled', 'parent_entry_id', 'parent_field_id', 'parent_col_id', 'parent_row_id', 'parent_var_id', 'parent_is_draft', 'child_entry_id', 'rel_order', 'rel_id');

    /**
     * Join tables
     * @var array
     */
    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
        'playa_relationships' => array('playa_relationships.child_entry_id', 'channel_titles.entry_id'),
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

        return $this->requireTable($query, 'playa_relationships', true)->whereIn('playa_relationships.parent_entry_id', $entryId);
    }
}
