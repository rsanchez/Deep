<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep;

use rsanchez\Deep\Container;
use CI_Controller;
use Closure;

/**
 * Singleton IoC Container Accessor
 */
final class Deep
{
    /**
     * Singleton instance
     * @var \rsanchez\Deep\Container
     */
    private static $container;

    /**
     * Private constructor so you cannot instantiate
     */
    private function __construct()
    {
    }

    /**
     * Bootstrap the main models on the global instance
     * @return void
     */
    public static function boot()
    {
        self::getInstance()->boot();
    }

    /**
     * Set Eloquent to use CodeIgniter's DB connection
     *
     * Set Deep to use upload prefs from config.php,
     * rather than from DB, if applicable.
     *
     * @return void
     */
    public static function bootEE(CI_Controller $ee = null)
    {
        self::getInstance()->bootEE($ee);
    }

    /**
     * Extend an abstract type in the global instance
     * @param  string  $abstract
     * @param  Closure $closure
     * @return void
     */
    public static function extend($abstract, Closure $closure)
    {
        self::getInstance()->extend($abstract, $closure);
    }

    /**
     * Get the global, singleton instance of Deep
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(self::$container)) {
            self::$container = new Container();
        }

        return self::$container;
    }
}
