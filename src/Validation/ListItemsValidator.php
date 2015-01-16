<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\AbstractProperty;

class ListItemsValidator implements PropertyValidatorInterface
{
    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Model\AbstractProperty $property
     * @return array
     */
    public function getRules(AbstractProperty $property)
    {
        $listItems = $property->getListItems();

        $rules = [];

        if ($listItems) {
            $rules[] = 'in:'.implode(',', $listItems);
        }

        return $rules;
    }
}
