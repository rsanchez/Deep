<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\ChannelField;
use rsanchez\Deep\Model\Entry;
use stdClass;

class Fieldtype extends Model
{
    protected $table = 'fieldtypes';
    protected $primaryKey = 'fieldtype_id';

    protected static $hydrators = array(
        #'matrix' => '\\rsanchez\\Deep\\Model\\Fieldtype\\Matrix',
        'assets' => '\\rsanchez\\Deep\\Model\\Hydrator\\AssetsHydrator',
    );

    public function hydrateCollection(Collection $collection)
    {
        if (array_key_exists($this->name, self::$hydrators)) {
            $class = self::$hydrators[$this->name];

            $hydrator = new $class();

            $hydrator($collection);
        }
    }

    public function mutate(Entry $entry, ChannelField $field)
    {
        return $entry->getAttribute('field_id_'.$field->field_id);
    }
}
