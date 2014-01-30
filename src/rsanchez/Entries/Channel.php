<?php

namespace rsanchez\Entries;

use rsanchez\Entries\IoC;

class Channel
{
    public static function entries()
    {
        static $ioc;

        if (is_null($ioc)) {
            $ioc = new IoC();
        }

        return $ioc['entries'];
    }
}
