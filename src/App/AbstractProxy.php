<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\App;

use rsanchez\Deep\Deep;

/**
 * Static proxy to the IoC container
 */
abstract class AbstractProxy
{
    /**
     * Run whenever __callStatic is invoked.
     * Be sure to make idempotent when using this
     * @return mixd
     */
    protected static function boot()
    {
        // do stuff here
    }

    /**
     * Name of IoC accessor
     * @return string
     */
    protected static function getAccessor()
    {
        return '';
    }

    /**
     * Static method call handler
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        static::boot();

        return call_user_func_array(array(Deep::getInstance()->make(static::getAccessor()), $name), $args);
    }
}
