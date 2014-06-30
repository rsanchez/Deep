<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use Carbon\Carbon;

/**
 * Hydrator for the Date fieldtype
 */
class DateHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = $entity->getAttribute($property->getIdentifer());

        $value = $value ? Carbon::createFromFormat('U', $value) : null;

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
