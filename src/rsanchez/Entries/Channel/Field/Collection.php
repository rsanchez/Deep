<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use rsanchez\Entries\PropertyCollection;

class Collection extends PropertyCollection
{
    protected $filterClass = __CLASS__;

    public function push(Field $field)
    {
        parent::push($field);
    }
}
