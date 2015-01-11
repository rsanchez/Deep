<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\AbstractProperty;
use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Validation\Validator as IlluminateValidator;

class Validator extends IlluminateValidator
{
    /**
     * Validate y or n
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validateYesOrNo($attribute, $value, $parameters = [])
    {
        return $value === 'y' || $value === 'n';
    }
}
