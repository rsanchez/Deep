<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\FieldCollection;

class Field extends Model
{
    protected $table = 'channel_fields';
    protected $primaryKey = 'group_id';

    public function fieldtype()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Fieldtype', 'name', 'field_type');
    }

    public function newCollection(array $fields = array())
    {
        return new FieldCollection($fields);
    }
}
