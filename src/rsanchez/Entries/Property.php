<?php

namespace rsanchez\Entries;

abstract class Property
{
    public function __construct(\stdClass $row)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }
    }

    abstract function settings();

    abstract function id();

    abstract function type();

    abstract function name();
}
