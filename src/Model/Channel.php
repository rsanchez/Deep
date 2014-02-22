<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class Channel extends Model
{
    protected $table = 'channels';
    protected $primaryKey = 'channel_id';

    public function fields()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\Field', 'group_id', 'field_group');
    }

    public function fieldsByType($type)
    {
        return $this->fields->filter(function ($field) use ($type) {
            return $field->field_type === $type;
        });
    }
}
