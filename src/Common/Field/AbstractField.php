<?php

namespace rsanchez\Deep\Common\Field;

use rsanchez\Deep\Property\AbstractProperty;

class AbstractField
{
    protected $property;
    public $value;

    public function __construct(
        $value,
        AbstractProperty $property
    ) {
        $this->property = $property;
        $this->value = $value;
    }

    public function settings()
    {
        return $this->property->settings();
    }

    public function id()
    {
        return $this->property->id();
    }

    public function type()
    {
        return $this->property->type();
    }

    public function name()
    {
        return $this->property->name();
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function __invoke()
    {
        return $this->__toString();
    }
}
