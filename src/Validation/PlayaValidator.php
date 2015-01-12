<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\AbstractProperty;

class PlayaValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\AbstractProperty $property
     * @return array
     */
    public function getRules(AbstractProperty $property)
    {
        $settings = $property->getSettings();

        $rules = [];

        if (! isset($settings['multi']) && $settings['multi'] === 'n') {
            $rules[] = 'max_count:1';
        }

        if (isset($settings['channels'])) {
            $rules[] = 'nested_attribute_in:channel_id,'.implode(',', $settings['channels']);
        }

        return $rules;
    }
}
