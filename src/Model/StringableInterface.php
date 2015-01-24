<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

interface StringableInterface
{
    /**
     * Set the string value
     * @param  string $value
     * @return void
     */
    public function setValue($value);

    /**
     * Get the string representation of this object.
     * Typically, this would be the "raw" value.
     * @return string
     */
    public function getValue();

    /**
     * Get the string representation of this object.
     * Typically, this would be the "transformed" value.
     * @return string
     */
    public function __toString();
}
