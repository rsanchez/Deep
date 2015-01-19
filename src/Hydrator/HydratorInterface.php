<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Collection\EntryCollection;

/**
 * Hydrator interface
 *
 * Hydrators bind custom fields properties to Entry objects
 */
interface HydratorInterface
{
    /**
     * Preload any custom field data that resides in another DB table
     * @param \rsanchez\Deep\Collection\EntryCollection  $collection
     * @return void
     */
    public function preload(EntryCollection $collection);

    /**
     * Hydrate the specified property (channel field or Matrix/Grid col)
     * on the specified entity (channel entry or Matrix/Grid row)
     *
     * @param  AbstractEntity   $entity
     * @param  PropertyInterface $property
     * @return mixed            the entity property value
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property);
}
