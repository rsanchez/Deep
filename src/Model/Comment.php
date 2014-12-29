<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the comments table
 */
class Comment extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'comment_id';

    /**
     * Define the Author Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Member', 'member_id', 'author_id');
    }

    /**
     * Get the comment_date column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getCommentDateAttribute($value)
    {
        return Carbon::createFromFormat('U', $value);
    }

    /**
     * Get the edit_date column as a Carbon object
     *
     * @param  int                 $value unix time
     * @return \Carbon\Carbon|null
     */
    public function getEditDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('U', $value) : null;
    }
}
