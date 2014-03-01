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
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\FileCollection;

/**
 * Model for the files table
 */
class File extends Model implements FileInterface
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'files';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'file_id';

    /**
     * Define the Upload Preferences Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploadPref()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\UploadPref', 'id', 'upload_location_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlAttribute()
    {
        return $this->uploadPref->url.$this->file_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerPathAttribute()
    {
        return $this->uploadPref->server_path.$this->file_name;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getUrlAttribute();
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                    $models
     * @return \rsanchez\Deep\Collection\FileCollection
     */
    public function newCollection(array $models = array())
    {
        return new FileCollection($models);
    }

    /**
     * Filter by files belonging to an EntryCollection
     *
     * EE doesn't actually have a DB of entries => files, so you have to
     * look up from the exp_files table based on filename and upload dir
     *
     * @param  \Illuminate\Database\Eloquent\Builder     $query
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

        return $query;
    }
}
