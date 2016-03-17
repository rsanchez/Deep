<?php

namespace rsanchez\Deep\App\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use rsanchez\Deep\Container;
use rsanchez\Deep\Model\Model;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->instance('rsanchez\Deep\Container', new Container());
        $this->app->alias('rsanchez\Deep\Container', 'deep');
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($connection = $this->app['config']->get('database.deep.connection')) {
            Model::setGlobalConnection($connection);
        }

        $this->app['deep']->boot();
    }
}
