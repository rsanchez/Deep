<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\CodeIgniter\CodeIgniterConnectionResolver;
use rsanchez\Deep\Model\Model;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\ChannelData;
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
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Validation\PresenceVerifierInterface;
use Illuminate\Validation\Factory as IlluminateValidationFactory;
use Symfony\Component\Translation\TranslatorInterface;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Validation\DatabasePresenceVerifier;
use rsanchez\Deep\Validation\Validator;
use Carbon\Carbon;
use CI_Controller;
use Closure;

/**
 * IoC Container
 */
class Container extends IlluminateContainer
{
    /**
     * Constructor
     *
     * Build all the dependencies
     *
     * @var array $config  an EE config array
     */
    public function __construct($config = [])
    {
        Carbon::setToStringFormat(Carbon::ISO8601);

        $this->singleton('config', function ($app) use ($config) {
            return $config;
        });

        $this->bind(ConnectionInterface::class, function ($app) {
            return Model::resolveConnection(Model::getGlobalConnection());
        });

        $this->alias(ConnectionInterface::class, 'db');

        $this->singleton(PresenceVerifierInterface::class, function ($app) {
            return new DatabasePresenceVerifier(Model::getConnectionResolver());
        });

        $this->alias(PresenceVerifierInterface::class, 'ValidationPresenceVerifier');

        $this->singleton(TranslatorInterface::class, function ($app) {
            return new Translator(new ArrayLoader, 'en');
        });

        $this->alias(TranslatorInterface::class, 'ValidationTranslator');

        $this->singleton(IlluminateValidationFactory::class, function ($app) {
            $validatorFactory = new ValidatorFactory($app->make('ValidationTranslator'));

            $validatorFactory->setPresenceVerifier($app->make('ValidationPresenceVerifier'));

            return $validatorFactory;
        });

        $this->alias(IlluminateValidationFactory::class, 'ValidatorFactory');

        $this->singleton(Field::class, function ($app) {
            $model = new Field();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(Field::class, 'Field');

        $this->singleton(FieldRepositoryInterface::class, function ($app) {
            return new FieldRepository($app->make('Field'), $app->make('db'));
        });

        $this->alias(FieldRepositoryInterface::class, 'FieldRepository');

        $this->singleton(Channel::class, function ($app) {
            $channel = new Channel();

            $channel->setFieldRepository($app->make('FieldRepository'));
            $channel->setValidatorFactory($app->make('ValidatorFactory'));

            return $channel;
        });

        $this->alias(Channel::class, 'Channel');

        $this->singleton(Site::class, function ($app) {
            $model = new Site();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(Site::class, 'Site');

        $this->singleton(UploadPref::class, function ($app) {
            $model = new UploadPref();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(UploadPref::class, 'UploadPref');

        $this->singleton(CategoryField::class, function ($app) {
            $model = new CategoryField();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(CategoryField::class, 'CategoryField');

        $this->singleton(MemberField::class, function ($app) {
            $model = new MemberField();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(MemberField::class, 'MemberField');

        $this->singleton(CategoryFieldRepositoryInterface::class, function ($app) {
            return new CategoryFieldRepository($app->make('CategoryField'));
        });

        $this->alias(CategoryFieldRepositoryInterface::class, 'CategoryFieldRepository');

        $this->singleton(MemberFieldRepository::class, function ($app) {
            return new MemberFieldRepository($app->make('MemberField'));
        });

        $this->alias(MemberFieldRepository::class, 'MemberFieldRepository');

        $this->singleton(ChannelRepositoryInterface::class, function ($app) {
            return new ChannelRepository($app->make('Channel'));
        });

        $this->alias(ChannelRepositoryInterface::class, 'ChannelRepository');

        $this->singleton(SiteRepositoryInterface::class, function ($app) {
            return new SiteRepository($app->make('Site'));
        });

        $this->alias(SiteRepositoryInterface::class, 'SiteRepository');

        $this->singleton(UploadPrefRepository::class, function ($app) {
            if (isset($app['config']['upload_prefs'])) {
                return new ConfigUploadPrefRepository($app->make('UploadPref'), $app['config']['upload_prefs']);
            }

            return new UploadPrefRepository($app->make('UploadPref'));
        });

        $this->alias(UploadPrefRepository::class, 'UploadPrefRepository');

        $this->singleton(Asset::class, function ($app) {
            $model = new Asset();

            $model->setUploadPrefRepository($app->make('UploadPrefRepository'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(Asset::class, 'Asset');

        $this->singleton(File::class, function ($app) {
            $model = new File();

            $model->setUploadPrefRepository($app->make('UploadPrefRepository'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(File::class, 'File');

        $this->singleton(GridCol::class, function ($app) {
            $model = new GridCol();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(GridCol::class, 'GridCol');

        $this->singleton(GridRow::class, function ($app) {
            $model = new GridRow();

            $model->setFieldRepository($app->make('FieldRepository'));
            $model->setHydratorFactory($app->make('RowHydratorFactory'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(GridRow::class, 'GridRow');

        $this->singleton(MatrixCol::class, function ($app) {
            $model = new MatrixCol();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(MatrixCol::class, 'MatrixCol');

        $this->singleton(MatrixRow::class, function ($app) {
            $model = new MatrixRow();

            $model->setFieldRepository($app->make('FieldRepository'));
            $model->setHydratorFactory($app->make('RowHydratorFactory'));
            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(MatrixRow::class, 'MatrixRow');

        $this->singleton(PlayaEntry::class, function ($app) {
            $model = new PlayaEntry();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(PlayaEntry::class, 'PlayaEntry');

        $this->singleton(RelationshipEntry::class, function ($app) {
            $model = new RelationshipEntry();

            $model->setValidatorFactory($app->make('ValidatorFactory'));

            return $model;
        });

        $this->alias(RelationshipEntry::class, 'RelationshipEntry');

        $this->singleton(RowHydratorFactory::class, function ($app) {
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

        $this->alias(RowHydratorFactory::class, 'RowHydratorFactory');

        $this->singleton(EntryHydratorFactory::class, function ($app) {
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

        $this->alias(EntryHydratorFactory::class, 'EntryHydratorFactory');

        $this->singleton(Category::class, function ($app) {
            $category = new Category();

            $category->setCategoryFieldRepository($app->make('CategoryFieldRepository'));
            $category->setChannelRepository($app->make('ChannelRepository'));
            $category->setValidatorFactory($app->make('ValidatorFactory'));

            return $category;
        });

        $this->alias(Category::class, 'Category');

        $this->singleton(Member::class, function ($app) {
            $member = new Member();

            $member->setMemberFieldRepository($app->make('MemberFieldRepository'));
            $member->setValidatorFactory($app->make('ValidatorFactory'));

            return $member;
        });

        $this->alias(Member::class, 'Member');

        $this->singleton(ChannelData::class, function ($app) {
            $channelData = new ChannelData();

            $channelData->setFieldRepository($app->make('FieldRepository'));

            return $channelData;
        });

        $this->alias(ChannelData::class, 'ChannelData');

        $this->singleton(Entry::class, function ($app) {
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

        $this->alias(Entry::class, 'Entry');
    }

    /**
     * Bootstrap the main models
     */
    public function boot()
    {
        $this->make('Entry');
    }

    /**
     * Set Eloquent to use CodeIgniter's DB connection
     *
     * Set Deep to use upload prefs from config.php,
     * rather than from DB, if applicable.
     *
     * @return void
     */
    public function bootEE(CI_Controller $ee = null)
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

        $this->extend('config', function ($app) use ($ee) {
            return $ee->config->config;
        });

        $uploadPrefs = $ee->config->item('upload_preferences');

        if ($uploadPrefs) {
            $this->extend('UploadPrefRepository', function ($app) use ($uploadPrefs) {
                return new ConfigUploadPrefRepository($app->make('UploadPref'), $uploadPrefs);
            });
        }

        $this->boot();

        $booted = true;
    }
}
