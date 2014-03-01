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
     * Join tables
     * @var array
     */
    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
        'playa_relationships' => array('playa_relationships.child_entry_id', 'channel_titles.entry_id'),
    );

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

        return $this->requireTable($query, 'playa_relationships')->whereIn('playa_relationships.parent_entry_id', $entryId);
    }
}
