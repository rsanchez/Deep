<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Entity\Field\Field;
use rsanchez\Deep\Common\Field\AbstractCollection;

class Collection extends AbstractCollection
{
    public function push(Field $field)
    {
        parent::push($field);
    }
}
