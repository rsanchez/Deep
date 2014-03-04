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
 * Model for the categories table
 */
class Category extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'cat_id';
}
