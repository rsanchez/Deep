<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\AssetCollection;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Relations\HasOneFromRepository;
use Carbon\Carbon;

/**
 * Model for the assets_files table, joined with assets_selections
 */
class Asset extends Model implements FileInterface
{
    use JoinableTrait, HasUploadPrefRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    const CREATED_AT = 'date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'date_modified';

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

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
    protected $hidden = [
        'file_id',
        'folder_id',
        'source_type',
        'source_id',
        'filedir_id',
        'entry_id',
        'field_id',
        'col_id',
        'row_id',
        'var_id',
        'element_id',
        'content_type',
        'sort_order',
        'is_draft',
        'uploadPref',
        'source_type',
        'folder_name',
        'full_path',
        'parent_id',
        'name',
        'settings',
    ];

    /**
     * {@inheritdoc}
     */
    protected $appends = [
        'url',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributeNames = [
        'folder_id' => 'Folder ID',
        'source_type' => 'Source Type',
        'source_id' => 'Source ID',
        'filedir_id' => 'Filedir ID',
        'file_name' => 'File Name',
        'kind' => 'Kind',
        'width' => 'Width',
        'height' => 'Height',
        'size' => 'Size',
    ];

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'folder_id' => 'required|exists:assets_folders,folder_id',
        'source_type' => 'required',
        'source_id' => 'required_if:source_type,rs,s3,gc|exists:assets_sources,source_id',
        'filedir_id' => 'required_if:source_type,ee|exists:upload_prefs,id',
        'file_name' => 'required',
        'kind' => 'required|in:access,audio,excel,flash,html,illustrator,image,pdf,photoshop,php,text,video,word',
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'required|integer',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'source_type' => 'ee',
    ];

    /**
     * {@inheritdoc}
     */
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = parent::getInsertValidationRules($validatorFactory, $property);

        $rules['date'] = 'required|date_format:U';

        return $rules;
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
            'filedir_id',
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

        $this->attributes['filedir_id'] = $uploadPref->id;
        $this->attributes['source_type'] = 'ee';
        $this->attributes['source_id'] = null;
    }

    /**
     * Set the filedir_id attribute for this entry
     * @param $filedirId
     */
    public function setUploadLocationIdAttribute($filedirId)
    {
        $this->setUploadPref(static::getUploadPrefRepository()->find($filedirId));
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $assets
     * @return \rsanchez\Deep\Collection\AssetCollection
     */
    public function newCollection(array $assets = [])
    {
        return new AssetCollection($assets);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlAttribute()
    {
        if (is_null($this->uploadPref) && $this->source_settings) {
            $base = $this->source_settings->url_prefix;

            if (! empty($this->source_settings->subfolder)) {
                $base .= $this->source_settings->subfolder.'/';
            }
        } else {
            $base = $this->uploadPref->url;
        }

        return $base.$this->full_path.$this->file_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerPathAttribute()
    {
        if (is_null($this->uploadPref) && $this->source_settings) {
            return;
        }

        return $this->uploadPref->server_path.$this->full_path.$this->file_name;
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
        $entryId = is_array($entryId) ? $entryId : [$entryId];

        return $this->requireTable($query, 'assets_selections')->whereIn('assets_selections.entry_id', $entryId);
    }

    /**
     * Filter by File ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $fileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFileId(Builder $query, $fileId)
    {
        $fileId = is_array($fileId) ? $fileId : [$fileId];

        return $this->whereIn('assets_files.file_id', $fileId);
    }

    /**
     * Get the json decoded settings for the source
     * @param  string $value json settings
     * @return array
     */
    public function getSourceSettingsAttribute($value)
    {
        return json_decode($this->settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach (['date', 'date_modified'] as $key) {
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
    public static function defaultJoinTables()
    {
        return ['assets_sources', 'assets_folders'];
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return [
            'assets_selections' => function ($query) {
                $query->join('assets_selections', 'assets_selections.file_id', '=', 'assets_files.file_id');
            },
            'assets_folders' => function ($query) {
                $query->join('assets_folders', 'assets_folders.folder_id', '=', 'assets_files.folder_id');
            },
            'assets_sources' => function ($query) {
                $query->leftJoin('assets_sources', 'assets_sources.source_id', '=', 'assets_files.source_id');
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function shouldValidateIfChild()
    {
        return ! $this->entry_id || ! $this->exists;
    }
}
