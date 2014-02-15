<?php

namespace rsanchez\Deep\Property;

use rsanchez\Deep\Property\AbstractProperty;
use SplObjectStorage;

abstract class AbstractCollection extends SplObjectStorage
{
    protected $filterClass;

    protected $propertiesByName = array();
    protected $propertiesById = array();

    /**
     * A shortcut to the field by name
     * @param  string $name the field_name attribute of the field
     * @return Field;
     */
    public function __get($name)
    {
        return $this->find($name);
    }

    public function find($id)
    {
        if (is_numeric($id)) {
            //@TODO custom exception
            if (! array_key_exists($id, $this->propertiesById)) {
                throw new \Exception('invalid field id');
            }

            return $this->propertiesById[$id];
        }

        if (! array_key_exists($id, $this->propertiesByName)) {
            throw new \Exception('invalid field name');
        }

        return $this->propertiesByName[$id];
    }

    public function filterByType($type)
    {
        $collectionClass = $this->filterClass ?: get_class($this);

        $collection = new $collectionClass();

        foreach ($this as $property) {
            if ($property->type() === $type) {
                $collection->attach($property);
            }
        }

        return $collection;
    }

    public function fieldIds()
    {
        return array_keys($this->propertiesById);
    }

    public function attach(AbstractProperty $property)
    {
        $this->propertiesById[$property->id()] =& $property;
        $this->propertiesByName[$property->name()] =& $property;

        return parent::attach($property);
    }
}
