<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AssetsFile extends Model
{
    protected $table = 'assets_files';
    protected $primaryKey = 'file_id';

    public function uploadPref()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\UploadPref', 'id', 'filedir_id');
    }

    public function getUrlAttribute()
    {
        return $this->uploadPref->url.$this->file_name;
    }

    public function getServerPathAttribute()
    {
        return $this->uploadPref->server_path.$this->file_name;
    }

    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'assets_selections')->whereIn('assets_selections.entry_id', $entryId);
    }

    protected function requireTable(Builder $query, $which)
    {
        static $tables = array(
            'assets_selections' => array('assets_selections.file_id', 'assets_files.file_id'),
        );

        if (! isset($tables[$which])) {
            return $query;
        }

        if (isset($query->getQuery()->joins)) {
            foreach ($query->getQuery()->joins as $joinClause) {
                if ($joinClause->table === $which) {
                    return $query;
                }
            }
        }

        return $query->join($which, $tables[$which][0], '=', $tables[$which][1]);
    }
}
