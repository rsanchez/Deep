<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\PropertyInterface;

class FieldpackMultiOptionsValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\PropertyInterface $property
     * @return array
     */
    public function getRules(PropertyInterface $property)
    {
        $settings = $property->getSettings();

        if (empty($settings['options'])) {
            return [];
        }

        return [
            'in_array:'.implode(',', array_keys($settings['options'])),
        ];
    }
}
