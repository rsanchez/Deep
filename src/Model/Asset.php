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
use rsanchez\Deep\Model\JoinableTrait;
use rsanchez\Deep\Model\FileInterface;
use rsanchez\Deep\Collection\AssetCollection;
use rsanchez\Deep\Model\UploadPref;
use Carbon\Carbon;

/**
 * Model for the assets_files table, joined with assets_selections
 */
class Asset extends Model implements FileInterface
{
    use JoinableTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'assets_files';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'file_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = array('file_id', 'folder_id', 'source_type', 'source_id', 'filedir_id', 'entry_id', 'field_id', 'col_id', 'row_id', 'var_id', 'element_id', 'content_type', 'sort_order', 'is_draft', 'uploadPref');

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
     *
     * @param  array                                     $assets
     * @return \rsanchez\Deep\Collection\AssetCollection
     */
    public function newCollection(array $assets = array())
    {
        return new AssetCollection($assets);
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
     * Get the date column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getDateAttribute($value)
    {
        return Carbon::createFromFormat('U', $value);
    }

    /**
     * Get the date_modified column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getDateModifiedAttribute($value)
    {
        return Carbon::createFromFormat('U', $value);
    }

    /**
     * Filter by Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'assets_selections')->whereIn('assets_selections.entry_id', $entryId);
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach (array('date', 'date_modified') as $key) {
            if (isset($attributes[$key]) && $attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $attributes;
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
     */
    protected static function joinTables()
    {
        return array(
            'assets_selections' => function ($query) {
                $query->join('assets_selections', 'assets_selections.file_id', '=', 'assets_files.file_id');
            },
        );
    }
}
