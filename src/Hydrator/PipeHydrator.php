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
 * Hydrator for the pipe delimited fields
 */
class PipeHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->{$property->getIdentifier()};

        return $value ? explode('|', $value) : null;
    }
}
