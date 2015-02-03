<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\PropertyInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\PropertyInterface
 */
class PropertyCollection extends AbstractModelCollection
{
    /**
     * array of field_name => \rsanchez\Deep\Model\PropertyInterface
     * @var array
     */
    protected $propertiesByName = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    /**
     * {@inheritdoc}
     * @param \rsanchez\Deep\Model\PropertyInterface $item
     */
    public function addModel(Model $item)
    {
        $this->addProperty($item);
    }

    /**
     * Add an PropertyInterface to this collection
     * @param  \rsanchez\Deep\Model\PropertyInterface $item
     * @return void
     */
    public function addProperty(PropertyInterface $item)
    {
        $this->propertiesByName[$item->getName()] = $item;

        $this->items[] = $item;
    }

    /**
     * Check if this collection has the specified property name
     *
     * @param  string  $name the name of the field
     * @return boolean
     */
    public function hasProperty($name)
    {
        return array_key_exists($name, $this->propertiesByName);
    }

    /**
     * Get the ID for the specified property name
     *
     * @param  string $name name of the property
     * @return string|null
     */
    public function getPropertyId($name)
    {
        return isset($this->propertiesByName[$name]) ? $this->propertiesByName[$name]->getId() : null;
    }

    /**
     * Get a property form this collection by its name
     *
     * @param  $name
     * @return \rsanchez\Deep\Model\PropertyInterface|null
     */
    public function getPropertyByName($name)
    {
        return isset($this->propertiesByName[$name]) ? $this->propertiesByName[$name] : null;
    }
}
