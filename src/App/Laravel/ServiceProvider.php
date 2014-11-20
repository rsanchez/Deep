<?php

namespace rsanchez\Deep\App\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\AliasLoader;
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

        $this->app->singleton('deep', function () {
            return new Deep();
        });

        $this->app->singleton('deep.entry', function ($app) {
            return $app->make('deep')->make('Entry');
        });

        $this->app->singleton('deep.title', function ($app) {
            return $app->make('deep')->make('Title');
        });

        $this->app->singleton('deep.category', function ($app) {
            return $app->make('deep')->make('Category');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // register our Facade aliases
        $loader = AliasLoader::getInstance();
        $loader->alias('Entries', 'rsanchez\Deep\App\Laravel\Facade\Entries');
        $loader->alias('Titles', 'rsanchez\Deep\App\Laravel\Facade\Titles');
        $loader->alias('Categories', 'rsanchez\Deep\App\Laravel\Facade\Categories');
    }
}
