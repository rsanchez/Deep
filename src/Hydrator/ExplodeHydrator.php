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
        $value = $entity->{$property->getIdentifier()};

        return $value ? explode("\n", $value) : null;
    }
}
