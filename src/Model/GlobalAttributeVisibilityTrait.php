<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Trait for Eloquent models that allows you to set
 * the model's hidden or visible attributes globally
 * for all instances of the model.
 */
trait GlobalAttributeVisibilityTrait
{
    /**
     * Get an attribute array of all arrayable values.
     *
     * @param  array $values
     * @return array
     */
    protected function getArrayableItems(array $values)
    {
        $visible = $this->getGlobalVisible() ?: $this->visible;

        if ($visible && count($visible) > 0) {
            return array_intersect_key($values, array_flip($visible));
        }

        $hidden = $this->getGlobalHidden() ?: $this->hidden;

        return array_diff_key($values, array_flip($hidden));
    }

    /**
     * Get the global hidden attributes for all instances of this model.
     *
     * @return array
     */
    public static function getGlobalHidden()
    {
        return isset(static::$globalHidden) ? static::$globalHidden : [];
    }

    /**
     * Set the global hidden attributes for all instances of this model.
     *
     * @param  array $hidden
     * @return void
     */
    public static function setGlobalHidden(array $hidden)
    {
        static::$globalHidden = $hidden;
    }

    /**
     * Get the global visible attributes for all instances of this model.
     *
     * @return array
     */
    public static function getGlobalVisible()
    {
        return isset(static::$globalVisible) ? static::$globalVisible : [];
    }

    /**
     * Set the global visible attributes for all instances of this model.
     *
     * @param  array $visible
     * @return void
     */
    public static function setGlobalVisible(array $visible)
    {
        static::$globalVisible = $visible;
    }
}
