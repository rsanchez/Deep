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

/**
 * Dehydrator interface
 */
interface DehydratorInterface
{
    /**
     * Convert an entity's property back to saveable format and do any outside DB operations
     * @param  AbstractEntity        $entity
     * @param  PropertyInterface      $property
     * @param  AbstractEntity|null   $parentEntity
     * @param  PropertyInterface|null $parentProperty
     * @return mixed                 the entity property value
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null);
}
