<?php

namespace rsanchez\Deep\Common\Field;

use rsanchez\Deep\Property\AbstractProperty;

abstract class AbstractFactory
{
    /**
     * @return rsanchez\Deep\Common\Field\AbstractField
     */
    public function createField($value, AbstractProperty $property)
    {
        //@TODO revisit this, it had to be this way for now because abstract method signatures must match exactly
    }
}
