<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\PropertyInterface;

class GridValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\PropertyInterface $property
     * @return array
     */
    public function getRules(PropertyInterface $property)
    {
        $settings = $property->getSettings();

        $rules = [];

        if (! empty($settings['grid_min_rows'])) {
            $rules[] = 'min_count:'.$settings['grid_min_rows'];
        }

        if (! empty($settings['grid_max_rows'])) {
            $rules[] = 'max_count:'.$settings['grid_max_rows'];
        }

        return $rules;
    }
}
