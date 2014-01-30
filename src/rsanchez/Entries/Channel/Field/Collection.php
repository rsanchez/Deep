<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use rsanchez\Entries\Property\AbstractCollection as PropertyAbstractCollection;

class Collection extends PropertyAbstractCollection
{
    protected $filterClass = __CLASS__;

    public function push(Field $field)
    {
        parent::push($field);
    }
}
