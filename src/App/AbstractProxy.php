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
     * Name of IoC accessor
     * @var string
     */
    protected static $accessor = '';

    /**
     * Static method call handler
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        return call_user_func_array(array(Deep::getInstance()->make(static::$accessor), $name), $args);
    }
}
