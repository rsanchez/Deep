<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\PropertyInterface;

class FileValidator implements PropertyValidatorInterface
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

        if (isset($settings['field_content_type']) && $settings['field_content_type'] === 'image') {
            $rules[] = 'image_attribute';
        }

        if (isset($settings['allowed_directories']) && $settings['allowed_directories'] !== 'all') {
            $rules[] = 'attribute_in:upload_location_id,'.$settings['allowed_directories'];
        }

        return $rules;
    }
}
