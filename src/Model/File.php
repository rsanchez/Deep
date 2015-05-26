<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\FileCollection;
use rsanchez\Deep\Collection\GridColCollection;
use rsanchez\Deep\Collection\GridRowCollection;
use rsanchez\Deep\Collection\MatrixColCollection;
use rsanchez\Deep\Collection\MatrixRowCollection;
use rsanchez\Deep\Relations\HasOneFromRepository;
use Carbon\Carbon;

/**
 * Model for the files table
 */
class File extends Model implements FileInterface
{
    use HasUploadPrefRepositoryTrait;
    
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
    protected $hidden = ['site_id', 'upload_location_id', 'rel_path', 'uploaded_by_member_id', 'modified_by_member_id', 'uploadPref'];

    /**
     * {@inheritdoc}
     */
    protected $appends = ['url'];

    /**
     * {@inheritdoc}
     */
    const CREATED_AT = 'upload_date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'modified_date';

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'title' => 'required',
        'upload_location_id' => 'required|exists:upload_prefs,id',
        'rel_path' => 'required',
        'mime_type' => 'required',
        'file_name' => 'required',
        'file_size' => 'required|integer',
        'uploaded_by_member_id' => 'required|exists:members,member_id',
        'modified_by_member_id' => 'required|exists:members,member_id',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * Define the UploadPref Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasOneFromRepository
     */
    public function uploadPref()
    {
        return new HasOneFromRepository(
            static::getUploadPrefRepository()->getModel()->newQuery(),
            $this,
            'upload_prefs.id',
            'upload_location_id',
            static::getUploadPrefRepository()
        );
    }

    /**
     * Set the UploadPref
     * @var \rsanchez\Deep\Model\UploadPref $uploadPref
     * @return void
     */
    public function setUploadPref(UploadPref $uploadPref)
    {
        $this->setRelation('uploadPref', $uploadPref);

        $this->attributes['upload_location_id'] = $uploadPref->id;
    }

    /**
     * Set the upload_location_id attribute for this entry
     * @param $uploadLocationId
     */
    public function setUploadLocationIdAttribute($uploadLocationId)
    {
        $this->setUploadPref(static::getUploadPrefRepository()->find($uploadLocationId));
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
    public function newCollection(array $models = [])
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
                $value = $entry->{$field->getIdentifier()};

                $this->scopeFileTag($query, $value);
            }
        }

        return $query;
    }

    /**
     * Filter by files belonging to a set of Matrix Rows and Cols
     *
     * @param  \Illuminate\Database\Eloquent\Builder         $query
     * @param  \rsanchez\Deep\Collection\MatrixColCollection $cols
     * @param  \rsanchez\Deep\Collection\MatrixRowCollection $rows
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromMatrix(Builder $query, MatrixColCollection $cols, MatrixRowCollection $rows)
    {
        $fileCols = [];

        foreach ($cols as $col) {
            if ($col->col_type === 'file') {
                $fileCols[] = $col;
            }
        }

        if (! $fileCols) {
            return $query;
        }

        foreach ($rows as $row) {
            foreach ($fileCols as $col) {
                $value = $row->{$col->getIdentifier()};

                $this->scopeFileTag($query, $value);
            }
        }

        return $query;
    }

    /**
     * Filter by files belonging to a set of Matrix Rows and Cols
     *
     * @param  \Illuminate\Database\Eloquent\Builder       $query
     * @param  \rsanchez\Deep\Collection\GridColCollection $cols
     * @param  \rsanchez\Deep\Collection\GridRowCollection $rows
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromGrid(Builder $query, GridColCollection $cols, GridRowCollection $rows)
    {
        $fileCols = [];

        foreach ($cols as $col) {
            if ($col->col_type === 'file') {
                $fileCols[] = $col;
            }
        }

        if (! $fileCols) {
            return $query;
        }

        foreach ($rows as $row) {
            foreach ($fileCols as $col) {
                $value = $row->{$col->getIdentifier()};

                $this->scopeFileTag($query, $value);
            }
        }

        return $query;
    }

    /**
     * Add an OR WHERE to the query according to the specified "tag"
     *
     * A "tag" follows this format: {filedir_1}your_file.jpg
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $tag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFileTag(Builder $query, $tag)
    {
        if (! preg_match('#^{filedir_(\d+)}(.*)$#', $tag, $match)) {
            return $query;
        }

        $filedir = $match[1];
        $filename = $match[2];

        return $query->orWhere(function ($query) use ($filename, $filedir) {
            return $query->where('file_name', $filename)
                ->where('upload_location_id', $filedir);
        });
    }

    /**
     * Specify a file name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fileName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFileName(Builder $query, $fileName)
    {
        return $query->where('file_name', $fileName);
    }

    /**
     * Specify an upload pref ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|int                            $uploadPrefId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUploadPrefId(Builder $query, $uploadPrefId)
    {
        return $query->where('upload_location_id', $uploadPrefId);
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach (['upload_date', 'modified_date'] as $key) {
            if (isset($attributes[$key]) && $attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $attributes;
    }
}
