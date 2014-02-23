<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\Entry;

class RelationshipEntry extends Entry
{
    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
        'relationships' => array('playa_relationships.child_id', 'channel_titles.entry_id'),
    );

    /**
     * Parent Entry ID
     */
    public function scopeParentEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'relationships')->whereIn('relationships.parent_id', $entryId);
    }
}
