<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use rsanchez\Deep\Model\PropertyInterface;

interface ProvidesValidationRulesInterface
{
    /**
     * @return boolean
     */
    public function shouldValidateIfChild();

    /**
     * Get a list of attribute short name => label
     * @param  string $prefix
     * @return array
     */
    public function getAttributeNames($prefix = '');

    /**
     * Get a list of validation rules
     * @param  \rsanchez\Deep\Validation\Factory          $validatorFactory
     * @param  \rsanchez\Deep\Model\PropertyInterface|null $property
     * @return array
     */
    public function getValidationRules(Factory $validatorFactory, PropertyInterface $property = null);
}
