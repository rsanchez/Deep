<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Model for the members table
 */
class Member extends Model
{
    use JoinableTrait, HasMemberFieldRepositoryTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'member_id';

    /**
     * Join with member_data
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFields(Builder $query)
    {
        return $this->requireTable($query, 'member_data');
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return [
            'member_data' => function ($query) {
                $query->join('member_data', 'member_data.member_id', '=', 'members.member_id');
            },
        ];
    }

    /**
     * Alias custom field names
     *
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        if (! isset($this->attributes[$name]) && static::getMemberFieldRepository()->hasField($name)) {
            $name = 'm_field_id_'.static::getMemberFieldRepository()->getFieldId($name);
        }

        return parent::getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $array = parent::attributesToArray();

        foreach ($array as $key => $value) {
            if (strncmp($key, 'm_field_id_', 11) === 0) {
                $id = substr($key, 11);

                if (static::getMemberFieldRepository()->hasFieldId($id)) {
                    $array[static::getMemberFieldRepository()->getFieldName($id)] = $value;
                }

                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Return the screen_name when cast to string
     *
     * @var string
     */
    public function __toString()
    {
        return $this->screen_name;
    }
}
