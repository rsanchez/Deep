<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\AbstractProperty;

class FieldpackOptionsValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\AbstractProperty $property
     * @return array
     */
    public function getRules(AbstractProperty $property)
    {
        $settings = $property->getSettings();

        if (empty($settings['options'])) {
            return [];
        }

        return [
            'in:'.implode(',', array_keys($settings['options'])),
        ];
    }
}
