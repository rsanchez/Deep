<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\App\EE;

use rsanchez\Deep\Deep;
use rsanchez\Deep\App\AbstractProxy as BaseProxy;

/**
 * Static proxy to the IoC container
 */
abstract class AbstractProxy extends BaseProxy
{
    /**
     * Set Eloquent to use CodeIgniter's DB connection
     *
     * Set Deep to use upload prefs from config.php,
     * rather than from DB, if applicable.
     *
     * @return void
     */
    protected static function boot()
    {
        Deep::bootEE(\ee());
    }
}
