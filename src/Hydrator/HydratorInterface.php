<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\AbstractProperty;

/**
 * Hydrator interface
 *
 * Hydrators bind custom fields properties to Entry objects
 */
interface HydratorInterface
{
    /**
     * Preload any custom field data that resides in another DB table
     * @param  array $entryIds all the entry IDs in the collection (including related entries)
     * @return void
     */
    public function preload(array $entryIds);

    /**
     * Hydrate the specified property (channel field or Matrix/Grid col)
     * on the specified entity (channel entry or Matrix/Grid row)
     *
     * @param  AbstractEntity   $entity
     * @param  AbstractProperty $property
     * @return mixed            the entity property value
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property);
}
