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
 * Dehydrator for the File fieldtype
 */
class FileDehydrator extends AbstractDehydrator
{
    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null)
    {
        $file = $entity->{$property->getName()};

        return $file ? '{filedir_'.$file->upload_location_id.'}'.$file->file_name : null;
    }
}
