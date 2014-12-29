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
use rsanchez\Deep\Model\Title;
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
use rsanchez\Deep\Hydrator\HydratorFactory;
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

        $this->bind('db', function ($app) {
            return Model::resolveConnection(Model::getGlobalConnection());
        });

        $this->singleton('Field', function ($app) {
            return new Field();
        });

        $this->singleton('FieldRepository', function ($app) {
            return new FieldRepository($app->make('Field'));
        });

        $this->singleton('Channel', function ($app) {
            $channel = new Channel();

            $channel->setFieldRepository($app->make('FieldRepository'));

            return $channel;
        });

        $this->singleton('Site', function ($app) {
            return new Site();
        });

        $this->singleton('UploadPref', function ($app) {
            return new UploadPref();
        });

        $this->singleton('CategoryField', function ($app) {
            return new CategoryField();
        });

        $this->singleton('MemberField', function ($app) {
            return new MemberField();
        });

        $this->singleton('CategoryFieldRepository', function ($app) {
            return new CategoryFieldRepository($app->make('CategoryField'));
        });

        $this->singleton('MemberFieldRepository', function ($app) {
            return new MemberFieldRepository($app->make('MemberField'));
        });

        $this->singleton('ChannelRepository', function ($app) {
            return new ChannelRepository($app->make('Channel'));
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

        $this->singleton('Asset', function ($app) {
            return new Asset();
        });

        $this->singleton('File', function ($app) {
            return new File();
        });

        $this->singleton('GridCol', function ($app) {
            return new GridCol();
        });

        $this->singleton('GridRow', function ($app) {
            return new GridRow();
        });

        $this->singleton('MatrixCol', function ($app) {
            return new MatrixCol();
        });

        $this->singleton('MatrixRow', function ($app) {
            return new MatrixRow();
        });

        $this->singleton('PlayaEntry', function ($app) {
            return new PlayaEntry();
        });

        $this->singleton('RelationshipEntry', function ($app) {
            return new RelationshipEntry();
        });

        $this->singleton('HydratorFactory', function ($app) {
            return new HydratorFactory(
                $app->make('db'),
                $app->make('SiteRepository'),
                $app->make('UploadPrefRepository'),
                $app->make('Asset'),
                $app->make('File'),
                $app->make('GridCol'),
                $app->make('GridRow'),
                $app->make('MatrixCol'),
                $app->make('MatrixRow'),
                $app->make('PlayaEntry'),
                $app->make('RelationshipEntry')
            );
        });

        $this->singleton('Category', function ($app) {
            $category = new Category();

            $category->setCategoryFieldRepository($app->make('CategoryFieldRepository'));
            $category->setChannelRepository($app->make('ChannelRepository'));

            return $category;
        });

        $this->singleton('Member', function ($app) {
            $member = new Member();

            $member->setMemberFieldRepository($app->make('MemberFieldRepository'));

            return $member;
        });

        $this->singleton('Title', function ($app) {
            $app->make('Category');
            $app->make('Member');

            $title = new Title();

            $title->setChannelRepository($app->make('ChannelRepository'));
            $title->setSiteRepository($app->make('SiteRepository'));
            $title->setHydratorFactory($app->make('HydratorFactory'));

            return $title;
        });

        $this->singleton('Entry', function ($app) {
            $app->make('Title');

            $entry = new Entry();

            $entry->setFieldRepository($app->make('FieldRepository'));

            return $entry;
        });
    }

    /**
     * Bootstrap the EE db connection with Eloquent, once
     * @param  \CI_Controller $ee
     * @return void
     */
    public static function bootEloquent(CI_Controller $ee)
    {
        if (Model::getConnectionResolver() instanceof CodeIgniterConnectionResolver) {
            return;
        }

        Model::setConnectionResolver(new CodeIgniterConnectionResolver($ee));
    }

    /**
     * Set Eloquent to use CodeIgniter's DB connection
     *
     * Set Deep to use upload prefs from config.php,
     * rather than from DB, if applicable.
     *
     * @return void
     */
    public static function bootEE(CI_Controller $ee)
    {
        static $booted = false;

        if ($booted) {
            return;
        }

        if (! Model::getConnectionResolver() instanceof CodeIgniterConnectionResolver) {
            Model::setConnectionResolver(new CodeIgniterConnectionResolver($ee));
        }

        self::extendInstance('config', function ($app) use ($ee) {
            return $ee->config->config;
        });

        $uploadPrefs = $ee->config->item('upload_preferences');

        if ($uploadPrefs) {
            self::extendInstance('UploadPrefRepository', function ($app) use ($uploadPrefs) {
                return new ConfigUploadPrefRepository($uploadPrefs);
            });
        }

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
        self::getInstance()->extend($abstract, $closure);
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
