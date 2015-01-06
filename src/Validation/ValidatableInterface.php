<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

interface ValidatableInterface
{
    /**
     * Get a list of attribute short name => label
     * @param  string $prefix
     * @return array
     */
    public function getAttributeNames($prefix = '');

    /**
     * Get a list of attributes that should be validated
     * @return array
     */
    public function getValidatableAttributes();

    /**
     * Get a list of validation rules
     * @param  string $prefix
     * @param  bool   $required
     * @return array
     */
    public function getValidationRules($prefix = '', $required = false);

    /**
     * Validate and throw a ValidationException if invalid
     * @return bool
     */
    public function validateOrFail();

    /**
     * Validate according to the validation rules
     * @param  bool $exceptionOnFailure
     * @return bool
     */
    public function validate($exceptionOnFailure = false);
}
