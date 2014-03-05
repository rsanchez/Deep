<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep;

use Illuminate\Container\Container;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;

/**
 * IoC Container
 */
class Entries extends Container
{
    /**
     * Constructor
     *
     * Build all the dependencies
     */
    public function __construct()
    {
        $this->bind('Field', function ($app) {
            return new Field();
        });

        $this->bind('Channel', function ($app) {
            return new Channel();
        });

        $this->singleton('FieldRepository', function ($app) {
            return new FieldRepository($app->make('Field')->all());
        });

        $this->singleton('ChannelRepository', function ($app) {
            return new ChannelRepository($app->make('Channel')->all(), $app->make('FieldRepository'));
        });

        $this->singleton('Entry', function ($app) {
            Entry::setFieldRepository($app->make('FieldRepository'));
            Entry::setChannelRepository($app->make('ChannelRepository'));
            return new Entry();
        });
    }

    /**
     * Shortcut to the dependency-injected Entry model
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        static $app;

        if (is_null($app)) {
            $app = new static();
        }

        return call_user_func_array(array($app->make('Entry'), $name), $args);
    }
}
