<?php

namespace rsanchez\Deep\Property;

use rsanchez\Deep\Fieldtype\Fieldtype;
use stdClass;

abstract class AbstractProperty
{
    /**
     * @var Fieldtype $fieldtype
     */
    public $fieldtype;

    public function __construct(stdClass $row, Fieldtype $fieldtype)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        $this->fieldtype = $fieldtype;
    }

    public function fieldtype()
    {
        return call_user_func_array($this->fieldtype, func_get_args());
    }

    abstract public function inputName();

    abstract public function prefix();

    abstract public function settings();

    abstract public function id();

    abstract public function type();

    abstract public function name();
}
