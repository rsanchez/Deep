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
class PipeHydrator extends AbstractHydrator implements DehydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->{$property->getIdentifier()};

        return $value ? explode('|', $value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, AbstractProperty $property, AbstractEntity $parentEntity = null, AbstractProperty $parentProperty = null)
    {
        $value = $entity->{$property->getName()};

        return $value ? implode('|', $value) : null;
    }
}
