<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;

/**
 * Hydrator for the carriage return delimited fields
 */
class ExplodeHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->getAttribute($property->getIdentifier());

        $value = $value ? explode("\n", $value) : null;

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
