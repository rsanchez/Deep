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
use Carbon\Carbon;
use DateTime;

/**
 * Hydrator for the Date fieldtype
 */
class DateHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        $value = $entity->{$property->getIdentifier()};

        return $value ? Carbon::createFromFormat('U', $value) : null;
    }

    /**
     * Setter callback
     * @param  \Carbon\Carbon|\DateTime|string|null $value
     * @return \rsanchez\Deep\Collection\AssetCollection|null
     */
    public function setter($value = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof DateTime) {
            return Carbon::instance($value);
        }

        if (is_int($value) || preg_match('/^\d+$/', $value)) {
            return Carbon::createFromFormat('U', $value);
        }

        return new Carbon($value);
    }
}
