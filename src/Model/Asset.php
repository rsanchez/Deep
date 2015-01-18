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
use Carbon\Carbon;

/**
 * Model for the assets_files table, joined with assets_selections
 */
class Asset extends Model implements FileInterface
{
    use JoinableTrait;

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
    protected $hidden = array('file_id', 'folder_id', 'source_type', 'source_id', 'filedir_id', 'entry_id', 'field_id', 'col_id', 'row_id', 'var_id', 'element_id', 'content_type', 'sort_order', 'is_draft', 'uploadPref', 'source_type', 'folder_name', 'full_path', 'parent_id', 'name', 'settings');

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
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = parent::getInsertValidationRules($validatorFactory, $property);

        $rules['date'] = 'required|date_format:U';

        return $rules;
    }

    /**
     * Set the UploadPref
     * @var \rsanchez\Deep\Model\UploadPref $uploadPref|null
     * @return void
     */
    public function setUploadPref(UploadPref $uploadPref = null)
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
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->requireTable($query, 'assets_selections')->whereIn('assets_selections.entry_id', $entryId);
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
    public static function defaultJoinTables()
    {
        return ['assets_sources', 'assets_folders'];
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
            'assets_folders' => function ($query) {
                $query->join('assets_folders', 'assets_folders.folder_id', '=', 'assets_files.folder_id');
            },
            'assets_sources' => function ($query) {
                $query->leftJoin('assets_sources', 'assets_sources.source_id', '=', 'assets_files.source_id');
            },
        );
    }
}
