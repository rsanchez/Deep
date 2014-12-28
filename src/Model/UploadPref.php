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
     *
     * @param  array                                          $models
     * @return \rsanchez\Deep\Collection\UploadPrefCollection
     */
    public function newCollection(array $models = array())
    {
        return new UploadPrefCollection($models);
    }
}
