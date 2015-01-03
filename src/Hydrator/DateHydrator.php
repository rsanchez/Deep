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
use Carbon\Carbon;

/**
 * Hydrator for the Date fieldtype
 */
class DateHydrator extends AbstractHydrator implements DehydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->{$property->getIdentifier()};

        return $value ? Carbon::createFromFormat('U', $value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, AbstractProperty $property, AbstractEntity $parentEntity = null, AbstractProperty $parentProperty = null)
    {
        $date = $entity->{$property->getName()};

        return $date ? $date->format('U') : null;
    }
}
