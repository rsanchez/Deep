<?php

namespace rsanchez\Deep;

use rsanchez\Deep\IoC;

class Deep
{
    private static $ioc;

    private static function ioc()
    {
        if (is_null(self::$ioc)) {
            self::$ioc = new IoC();
        }

        return self::$ioc;
    }

    public static function entries()
    {
        $ioc = self::ioc();

        return $ioc[__FUNCTION__];
    }
}
