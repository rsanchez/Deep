<?php

namespace rsanchez\Entries\Property;

use stdClass;

abstract class AbstractProperty
{
    // exp_fieldtypes
    public $fieldtype_id;
    public $name;
    public $version;
    public $settings;
    public $has_global_settings;

    public function __construct(stdClass $row)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }
    }

    abstract public function settings();

    abstract public function id();

    abstract public function type();

    abstract public function name();
}
