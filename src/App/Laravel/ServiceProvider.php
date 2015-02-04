<?php

namespace rsanchez\Deep\App\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use rsanchez\Deep\Deep;
use rsanchez\Deep\Model\Model;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($connection = $this->app['config']->get('database.deep.connection')) {
            Model::setGlobalConnection($connection);
        }

        $this->app->singleton('deep', function ($app) {
            $deep = new Deep();

            $deep->boot();

            return $deep;
        });
    }
}
