<?php

namespace rsanchez\Deep\Entry\Field;

use rsanchez\Deep\Entity\Field\Field;
use rsanchez\Deep\Entity\Field\Collection as EntityFieldCollection;

class Collection extends EntityFieldCollection
{
    public function push(Field $field)
    {
        return parent::push($field);
    }
}
