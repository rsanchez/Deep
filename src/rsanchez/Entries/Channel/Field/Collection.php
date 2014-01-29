<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use rsanchez\Entries\PropertyCollection;

class Collection extends PropertyCollection
{
    public function filterByType($type)
    {
        $properties = array_filter($this->properties, function ($field) use ($type) {
            return $field->type() === $type;
        });

        $collection = new Collection();

        array_walk($properties, array($collection, 'push'));

        return $collection;
    }

    public function push(Field $field)
    {
        array_push($this->properties, $field);

        $this->propertiesById[$field->field_id] =& $field;
        $this->propertiesByName[$field->field_name] =& $field;
    }
}
