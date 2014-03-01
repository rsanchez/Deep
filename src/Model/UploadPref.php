<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;

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
}
