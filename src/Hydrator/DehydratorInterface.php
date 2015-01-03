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
 * Dehydrator interface
 */
interface DehydratorInterface
{
    /**
     * Convert an entity's property back to saveable format and do any outside DB operations
     * @param  AbstractEntity        $entity
     * @param  AbstractProperty      $property
     * @param  AbstractEntity|null   $parentEntity
     * @param  AbstractProperty|null $parentProperty
     * @return mixed                 the entity property value
     */
    public function dehydrate(AbstractEntity $entity, AbstractProperty $property, AbstractEntity $parentEntity = null, AbstractProperty $parentProperty = null);
}
