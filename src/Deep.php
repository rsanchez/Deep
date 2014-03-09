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
use rsanchez\Deep\Model\Title;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;
use rsanchez\Deep\Repository\ConfigUploadPrefRepository;
use rsanchez\Deep\Hydrator\HydratorFactory;

/**
 * IoC Container
 */
class Deep extends Container
{
    /**
     * Constructor
     *
     * Build all the dependencies
     *
     * @var array $config  an EE config array
     */
    public function __construct($config = array())
    {
        $this->singleton('config', function ($app) use ($config) {
            return $config;
        });

        $this->singleton('Field', function ($app) {
            return new Field();
        });

        $this->singleton('Channel', function ($app) {
            return new Channel();
        });

        $this->singleton('Site', function ($app) {
            return new Site();
        });

        $this->singleton('UploadPref', function ($app) {
            return new UploadPref();
        });

        $this->singleton('FieldRepository', function ($app) {
            return new FieldRepository($app->make('Field'));
        });

        $this->singleton('ChannelRepository', function ($app) {
            return new ChannelRepository($app->make('Channel'), $app->make('FieldRepository'));
        });

        $this->singleton('SiteRepository', function ($app) {
            return new SiteRepository($app->make('Site'));
        });

        $this->singleton('UploadPrefRepository', function ($app) {
            if (isset($app['config']['upload_prefs'])) {
                return new ConfigUploadPrefRepository($app['config']['upload_prefs']);
            }

            return new UploadPrefRepository($app->make('UploadPref'));
        });

        $this->singleton('HydratorFactory', function ($app) {
            return new HydratorFactory($app->make('SiteRepository'), $app->make('UploadPrefRepository'));
        });

        $this->singleton('Title', function ($app) {
            Title::setChannelRepository($app->make('ChannelRepository'));
            Title::setSiteRepository($app->make('SiteRepository'));

            return new Title();
        });

        $this->singleton('Entry', function ($app) {
            Entry::setFieldRepository($app->make('FieldRepository'));
            Entry::setChannelRepository($app->make('ChannelRepository'));
            Entry::setHydratorFactory($app->make('HydratorFactory'));
            Entry::setSiteRepository($app->make('SiteRepository'));

            return new Entry();
        });
    }

    /**
     * The static proxies Entries and Titles use this
     * @return static
     */
    public static function getInstance()
    {
        static $app;

        if (is_null($app)) {
            $app = new self();
        }

        return $app;
    }
}
