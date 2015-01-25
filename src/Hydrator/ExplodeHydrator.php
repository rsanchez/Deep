<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;

/**
 * Hydrator for the carriage return delimited fields
 */
class ExplodeHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        $value = $entity->{$property->getIdentifier()};

        return $value ? explode("\n", $value) : [];
    }

    /**
     * Setter callback
     * @param  array|null $value
     * @return array|null
     */
    public function setter(array $value = null)
    {
        return $value;
    }
}
