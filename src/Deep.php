<?php

namespace rsanchez\Deep;

use rsanchez\Deep\IoC;

/**
 * A static interface to the IoC
 */
class Deep
{
    public static function __callStatic($name, $args)
    {
        static $ioc;

        if (is_null($ioc)) {
            $ioc = new IoC();
        }

        return $ioc[$name];
    }

    public static function entries($params = array())
    {
        $entries = self::__callStatic('entries', null);

        $entries->applyParams($params);

        return $entries;
    }
}
