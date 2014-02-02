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

    public $preload = false;
    public $preloadHighPriority = false;

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

        if ($this->preload) {
            $collection->registerFieldPreloader($this->property->type(), $this, $this->preloadHighPriority);
        }
    }

    public function __invoke($value)
    {
        return $value;
    }

    public function preload(DbInterface $db, array $entryIds, array $fieldIds)
    {
    }

    public function postload($payload, EntityFieldFactory $entryFieldFactory, ColFactory $colFactory)
    {
    }
}
