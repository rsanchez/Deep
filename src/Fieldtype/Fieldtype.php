<?php

namespace rsanchez\Deep\Fieldtype;

use stdClass;

class Fieldtype
{
    public $fieldtype_id;
    public $name;
    public $version;
    public $settings;
    public $has_global_settings;

    public function __construct(stdClass $row)
    {
        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        if ($this->has_global_settings === 'y' && $this->settings) {
            $this->settings = unserialize(base64_decode($this->settings));
        } else {
            $this->settings = array();
        }
    }
}
