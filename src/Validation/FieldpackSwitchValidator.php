<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\AbstractProperty;

class FieldpackSwitchValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\AbstractProperty $property
     * @return array
     */
    public function getRules(AbstractProperty $property)
    {
        $settings = $property->getSettings();

        if ( ! isset($settings['on_val']) || ! isset($settings['off_val'])) {
            return [];
        }

        return [
            sprintf('in:%s,%s', $settings['on_val'], $settings['off_val']),
        ];
    }
}
