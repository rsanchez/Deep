<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\UploadPrefCollection;

/**
 * Model for the upload_prefs table
 */
class UploadPref extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'upload_prefs';

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'name' => 'required',
        'server_path' => 'required|is_dir',
        'url' => 'required',
        'allowed_types' => 'required|in:all,img',
        'max_size' => 'integer',
        'max_height' => 'integer',
        'max_width' => 'integer',
        'cat_group' => 'exists:category_groups,group_id',
    ];

    /**
     * {@inheritdoc}
     *
     * @param  array                                          $models
     * @return \rsanchez\Deep\Collection\UploadPrefCollection
     */
    public function newCollection(array $models = [])
    {
        return new UploadPrefCollection($models);
    }
}
