<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the category_posts table
 */
class CategoryPosts extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'category_posts';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'cat_id';

    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'cat_id' => 'required|exists:categories,cat_id',
        'entry_id' => 'required|exists:channel_titles,entry_id'
    ];
}
