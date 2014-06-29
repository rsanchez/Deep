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
use rsanchez\Deep\Model\UploadPref;
use Carbon\Carbon;
use DateTimeZone;

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
     * {@inheritdoc}
     */
    protected $hidden = array('site_id', 'upload_location_id', 'rel_path', 'uploaded_by_member_id', 'modified_by_member_id', 'uploadPref');

    /**
     * {@inheritdoc}
     */
    protected $appends = array('url');

    /**
     * UploadPref model
     * @var \rsanchez\Deep\Model\UploadPref
     */
    protected $uploadPref;

    /**
     * Set the UploadPref
     * @var \rsanchez\Deep\Model\UploadPref $uploadPref
     * @return void
     */
    public function setUploadPref(UploadPref $uploadPref)
    {
        $this->uploadPref = $uploadPref;
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
     * Get the upload_date column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getUploadDateAttribute($value)
    {
        return Carbon::createFromFormat('U', $value);
    }

    /**
     * Get the modified_date column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getModifiedDateAttribute($value)
    {
        return Carbon::createFromFormat('U', $value);
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
        foreach ($collection as $entry) {

            foreach ($entry->channel->fieldsByType('file') as $field) {

                $value = $entry->getAttribute('field_id_'.$field->field_id);

                if (! preg_match('#^{filedir_(\d+)}(.*)$#', $value, $match)) {
                    return;
                }

                $filedir = $match[1];
                $filename = $match[2];

                $query->orWhere(function ($query) use ($filedir, $filename) {
                    return $query->where('file_name', $filename)
                        ->where('upload_location_id', $filedir);
                });
            }

        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach (array('upload_date', 'modified_date') as $key) {
            if ($attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $attributes;
    }
}
