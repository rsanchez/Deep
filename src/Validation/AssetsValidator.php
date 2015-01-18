<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\PropertyInterface;

class AssetsValidator implements PropertyValidatorInterface
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

        if (! isset($settings['multi']) || $settings['multi'] !== 'y') {
            $rules[] = 'max_count:1';
        }

        if (isset($settings['filedirs']) && $settings['filedirs'] !== 'all') {
            // remove the colon, which will interfere with concatenating in the validator
            $filedirs = array_map(function ($item) {
                return str_replace(':', '', $item);
            }, $settings['filedirs']);

            $rules[] = 'nested_concatenated_attribute_in:source_type:folder_id,'.implode(',', $filedirs);
        }

        return $rules;
    }
}
