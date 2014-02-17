<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class ChannelField extends Model
{
    protected $table = 'channel_fields';
    protected $primaryKey = 'group_id';

    public function fieldtype()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Fieldtype', 'name', 'field_type');
    }
}
