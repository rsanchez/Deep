<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep;

use Illuminate\Container\Container;
use Illuminate\CodeIgniter\CodeIgniterConnectionResolver;
use rsanchez\Deep\Model\Model;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Model\Category;
use rsanchez\Deep\Model\Member;
use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Model\RelationshipEntry;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;
use rsanchez\Deep\Repository\ConfigUploadPrefRepository;
use rsanchez\Deep\Repository\CategoryFieldRepository;
use rsanchez\Deep\Repository\MemberFieldRepository;
use rsanchez\Deep\Hydrator\EntryHydratorFactory;
use rsanchez\Deep\Hydrator\RowHydratorFactory;
use Symfony\Component\Translation\Translator;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Validation\DatabasePresenceVerifier;
use rsanchez\Deep\Validation\Validator;
use Carbon\Carbon;
use CI_Controller;
use Closure;

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
        Carbon::setToStringFormat(Carbon::ISO8601);

        $this->singleton('config', function ($app) use ($config) {
            return $config;
        });

        $this->bind('Illuminate\Database\ConnectionInterface', function ($app) {
            return Model::resolveConnection(Model::getGlobalConnection());
        });

        $this->alias('Illuminate\Database\ConnectionInterface', 'db');

        $this->singleton('Illuminate\Validation\PresenceVerifierInterface', function ($app) {
            return new DatabasePresenceVerifier(Model::getConnectionResolver());
        });

        $this->alias('Illuminate\Validation\PresenceVerifierInterface', 'ValidationPresenceVerifier');

        $this->singleton('Symfony\Component\Translation\TranslatorInterface', function ($app) {
            return new Translator('en');
        });

        $this->alias('Symfony\Component\Translation\TranslatorInterface', 'ValidationTranslator');

        $this->singleton('Illuminate\Validation\Factory', function ($app) {
            $validatorFactory = new ValidatorFactory($app->make('ValidationTranslator'));

            $validatorFactory->setPresenceVerifier($app->make('ValidationPresenceVerifier'));

            return $validatorFactory;
        });

        $this->alias('Illuminate\Validation\Factory', 'ValidatorFactory');

        $this->singleton('rsanchez\Deep\Model\Field', function ($app) {
            $model = new Field();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\Field', 'Field');

        $this->singleton('rsanchez\Deep\Repository\FieldRepositoryInterface', function ($app) {
            return new FieldRepository($app->make('Field'));
        });

        $this->alias('rsanchez\Deep\Repository\FieldRepositoryInterface', 'FieldRepository');

        $this->singleton('rsanchez\Deep\Model\Channel', function ($app) {
            $channel = new Channel();

            $channel->setFieldRepository($app->make('FieldRepository'));
            $channel->setValidatorFactory($app->make('ValidatorFactory'));

            return $channel;
        });

        $this->alias('rsanchez\Deep\Model\Channel', 'Channel');

        $this->singleton('rsanchez\Deep\Model\Site', function ($app) {
            $model = new Site();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\Site', 'Site');

        $this->singleton('rsanchez\Deep\Model\UploadPref', function ($app) {
            $model = new UploadPref();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\UploadPref', 'UploadPref');

        $this->singleton('rsanchez\Deep\Model\CategoryField', function ($app) {
            $model = new CategoryField();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\CategoryField', 'CategoryField');

        $this->singleton('rsanchez\Deep\Model\MemberField', function ($app) {
            $model = new MemberField();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\MemberField', 'MemberField');

        $this->singleton('rsanchez\Deep\Repository\CategoryFieldRepositoryInterface', function ($app) {
            return new CategoryFieldRepository($app->make('CategoryField'));
        });

        $this->alias('rsanchez\Deep\Repository\CategoryFieldRepositoryInterface', 'CategoryFieldRepository');

        $this->singleton('rsanchez\Deep\Repository\MemberFieldRepository', function ($app) {
            return new MemberFieldRepository($app->make('MemberField'));
        });

        $this->alias('rsanchez\Deep\Repository\MemberFieldRepository', 'MemberFieldRepository');

        $this->singleton('rsanchez\Deep\Repository\ChannelRepositoryInterface', function ($app) {
            return new ChannelRepository($app->make('Channel'));
        });

        $this->alias('rsanchez\Deep\Repository\ChannelRepositoryInterface', 'ChannelRepository');

        $this->singleton('rsanchez\Deep\Repository\SiteRepositoryInterface', function ($app) {
            return new SiteRepository($app->make('Site'));
        });

        $this->alias('rsanchez\Deep\Repository\SiteRepositoryInterface', 'SiteRepository');

        $this->singleton('rsanchez\Deep\Repository\UploadPrefRepository', function ($app) {
            if (isset($app['config']['upload_prefs'])) {
                return new ConfigUploadPrefRepository($app->make('UploadPref'), $app['config']['upload_prefs']);
            }

            return new UploadPrefRepository($app->make('UploadPref'));
        });

        $this->alias('rsanchez\Deep\Repository\UploadPrefRepository', 'UploadPrefRepository');

        $this->singleton('rsanchez\Deep\Model\Asset', function ($app) {
            $model = new Asset();

            $model->setUploadPrefRepository($app->make('UploadPrefRepository'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\Asset', 'Asset');

        $this->singleton('rsanchez\Deep\Model\File', function ($app) {
            $model = new File();

            $model->setUploadPrefRepository($app->make('UploadPrefRepository'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\File', 'File');

        $this->singleton('rsanchez\Deep\Model\GridCol', function ($app) {
            $model = new GridCol();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\GridCol', 'GridCol');

        $this->singleton('rsanchez\Deep\Model\GridRow', function ($app) {
            $model = new GridRow();

            $model->setFieldRepository($app->make('FieldRepository'));
            $model->setHydratorFactory($app->make('RowHydratorFactory'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\GridRow', 'GridRow');

        $this->singleton('rsanchez\Deep\Model\MatrixCol', function ($app) {
            $model = new MatrixCol();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\MatrixCol', 'MatrixCol');

        $this->singleton('rsanchez\Deep\Model\MatrixRow', function ($app) {
            $model = new MatrixRow();

            $model->setFieldRepository($app->make('FieldRepository'));
            $model->setHydratorFactory($app->make('RowHydratorFactory'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\MatrixRow', 'MatrixRow');

        $this->singleton('rsanchez\Deep\Model\PlayaEntry', function ($app) {
            $model = new PlayaEntry();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\PlayaEntry', 'PlayaEntry');

        $this->singleton('rsanchez\Deep\Model\RelationshipEntry', function ($app) {
            $model = new RelationshipEntry();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias('rsanchez\Deep\Model\RelationshipEntry', 'RelationshipEntry');

        $this->singleton('rsanchez\Deep\Hydrator\RowHydratorFactory', function ($app) {
            return new RowHydratorFactory(
                $app->make('db'),
                $app->make('SiteRepository'),
                $app->make('UploadPrefRepository'),
                $app->make('Asset'),
                $app->make('File'),
                $app->make('PlayaEntry'),
                $app->make('RelationshipEntry')
            );
        });

        $this->alias('rsanchez\Deep\Hydrator\RowHydratorFactory', 'RowHydratorFactory');

        $this->singleton('rsanchez\Deep\Hydrator\EntryHydratorFactory', function ($app) {
            return new EntryHydratorFactory(
                $app->make('db'),
                $app->make('SiteRepository'),
                $app->make('UploadPrefRepository'),
                $app->make('Asset'),
                $app->make('File'),
                $app->make('PlayaEntry'),
                $app->make('RelationshipEntry'),
                $app->make('GridCol'),
                $app->make('GridRow'),
                $app->make('MatrixCol'),
                $app->make('MatrixRow')
            );
        });

        $this->alias('rsanchez\Deep\Hydrator\EntryHydratorFactory', 'EntryHydratorFactory');

        $this->singleton('rsanchez\Deep\Model\Category', function ($app) {
            $category = new Category();

            $category->setCategoryFieldRepository($app->make('CategoryFieldRepository'));
            $category->setChannelRepository($app->make('ChannelRepository'));
            $category->setValidatorFactory($app->make('ValidatorFactory'));

            return $category;
        });

        $this->alias('rsanchez\Deep\Model\Category', 'Category');

        $this->singleton('rsanchez\Deep\Model\Member', function ($app) {
            $member = new Member();

            $member->setMemberFieldRepository($app->make('MemberFieldRepository'));
            $member->setValidatorFactory($app->make('ValidatorFactory'));

            return $member;
        });

        $this->alias('rsanchez\Deep\Model\Member', 'Member');

        $this->singleton('rsanchez\Deep\Model\Entry', function ($app) {
            $app->make('Category');
            $app->make('Member');

            $entry = new Entry();

            $entry->setChannelRepository($app->make('ChannelRepository'));
            $entry->setFieldRepository($app->make('FieldRepository'));
            $entry->setSiteRepository($app->make('SiteRepository'));
            $entry->setHydratorFactory($app->make('EntryHydratorFactory'));
            $entry->setValidatorFactory($app->make('ValidatorFactory'));

            return $entry;
        });

        $this->alias('rsanchez\Deep\Model\Entry', 'Entry');
    }

    /**
     * Bootstrap the main models
     */
    public function boot()
    {
        $this->make('Entry');
    }

    /**
     * Bootstrap the main models on the global instance
     */
    public static function bootInstance()
    {
        Deep::getInstance()->boot();
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
        static $booted = false;

        if ($booted) {
            return;
        }

        if (is_null($ee)) {
            $ee = ee();
        }

        if (! Model::getConnectionResolver() instanceof CodeIgniterConnectionResolver) {
            Model::setConnectionResolver(new CodeIgniterConnectionResolver($ee));
        }

        static::extendInstance('config', function ($app) use ($ee) {
            return $ee->config->config;
        });

        $uploadPrefs = $ee->config->item('upload_preferences');

        if ($uploadPrefs) {
            static::extendInstance('UploadPrefRepository', function ($app) use ($uploadPrefs) {
                return new ConfigUploadPrefRepository($app->make('UploadPref'), $uploadPrefs);
            });
        }

        static::bootInstance();

        $booted = true;
    }

    /**
     * Extend an abstract type in the global instance
     * @param  string  $abstract
     * @param  Closure $closure
     * @return void
     */
    public static function extendInstance($abstract, Closure $closure)
    {
        static::getInstance()->extend($abstract, $closure);
    }

    /**
     * The static proxies Entries and Titles use this
     * @return static
     */
    public static function getInstance()
    {
        static $app;

        if (is_null($app)) {
            $app = new static();
        }

        return $app;
    }
}
