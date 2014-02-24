<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\Collection\EntryCollection;

class File extends Model implements FileInterface
{
    protected $table = 'files';
    protected $primaryKey = 'file_id';

    public function uploadPref()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\UploadPref', 'id', 'upload_location_id');
    }

    public function getUrlAttribute()
    {
        return $this->uploadPref->url.$this->file_name;
    }

    public function getServerPathAttribute()
    {
        return $this->uploadPref->server_path.$this->file_name;
    }

    public function __toString()
    {
        return $this->getUrlAttribute();
    }

    public function scopeFromEntryCollection(Builder $query, EntryCollection $collection)
    {
        // EE isn't PDO, so no prepared statements
        // I hate doing this...
        $escape = array($query->getQuery()->getConnection()->ci->db, 'escape');

        $collection->each(function ($entry) use ($query, $escape) {

            $entry->channel->fieldsByType('file')->each(function ($field) use ($entry, $query, $escape) {

                $value = $entry->getAttribute('field_id_'.$field->field_id);

                if (! preg_match('#^{filedir_(\d+)}(.*)$#', $value, $match)) {
                    return;
                }

                $filedir = call_user_func($escape, $match[1]);
                $filename = call_user_func($escape, $match[2]);

                $query->orWhereRaw("(`file_name` = {$filename} AND `upload_location_id` = {$filedir})");
            });

        });
    }
}
