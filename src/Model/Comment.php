<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Carbon\Carbon;

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
     * {@inheritdoc}
     */
    const CREATED_AT = 'comment_date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'edit_date';

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'site_id' => 1,
        'status' => 'o',
        'ip_address' => '127.0.0.1',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributeNames = [
        'site_id' => 'Site ID',
        'entry_id' => 'Entry ID',
        'channel_id' => 'Channel ID',
        'author_id' => 'Author ID',
        'status' => 'Status',
        'name' => 'Name',
        'email' => 'Email',
        'url' => 'URL',
        'ip_address' => 'IP Address',
        'comment_date' => 'Comment Date',
        'edit_date' => 'Edit Date',
        'comment' => 'Comment',
    ];

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'entry_id' => 'required|exists:channel_titles,entry_id',
        'channel_id' => 'required|exists:channels,channel_id',
        'author_id' => 'exists:members,member_id',
        'status' => 'required|in:o,c',
        'name' => 'required',
        'email' => 'email',
        'url' => 'url',
        'ip_address' => 'ip',
        'comment_date' => 'date_format:U',
        'edit_date' => 'date_format:U',
        'comment' => 'required',
    ];

    /**
     * Define the Author Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Member', 'member_id', 'author_id');
    }

    /**
     * Set the author for this entry
     * @param  \rsanchez\Deep\Model\Member $member
     * @return void
     */
    public function setAuthor(Member $member)
    {
        $this->attributes['author_id'] = $member->member_id;

        $this->setRelation('author', $member);
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

        foreach (['comment_date', 'edit_date'] as $key) {
            if (isset($attributes[$key]) && $attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $attributes;
    }
}
