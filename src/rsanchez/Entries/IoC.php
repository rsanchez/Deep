<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Db;
use rsanchez\Entries\Channel\Factory as ChannelFactory;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Channel\Field\Group as FieldGroup;
use rsanchez\Entries\FilePath;
use rsanchez\Entries\FilePath\Factory as FilePathFactory;
use rsanchez\Entries\FilePath\Storage as FilePathStorage;
use rsanchez\Entries\Entry\Field\CollectionFactory as EntryFieldCollectionFactory;
use rsanchez\Entries\Channel\Field\GroupFactory as FieldGroupFactory;
use rsanchez\Entries\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Entries\Channel\Fields;
use rsanchez\Entries\Channel\Storage as ChannelStorage;
use rsanchez\Entries\Channel\Field\Storage as FieldStorage;
use rsanchez\Entries\Entry;
use rsanchez\Entries\Entry\Field as EntryField;
use rsanchez\Entries\Model;
use rsanchez\Entries\Entry\Field\Factory as EntryFieldFactory;
use rsanchez\Entries\Entry\Factory as EntryFactory;
use \Pimple;

class IoC extends Pimple
{
    public function __construct()
    {
        parent::__construct();

        $this['baseUrl'] = ee()->config->item('site_url');

        $this['db'] = $this->factory(function () {
            static $count = 1;

            $db = new Db(array(
                'dbdriver' => 'mysql',
                'conn_id'  => ee()->db->conn_id,
                'database' => ee()->db->database,
                'dbprefix' => ee()->db->dbprefix,
            ));

            // log queries
            $db->save_queries = ee()->config->item('show_profiler') === 'y' || DEBUG === 1;

            // attach back to ee so the profiler knows to show these queries
            ee()->{'db'.$count} = $db;

            $count++;

            return $db;
        });

        $this['filePathStorage'] = function ($container) {
            return new FilePathStorage($container['db'], $container['ee']->config->item('upload_prefs'));
        };

        $this['fieldStorage'] = function ($container) {
            return new FieldStorage($container['db']);
        };

        $this['channelStorage'] = function ($container) {
            return new ChannelStorage($container['db']);
        };

        $this['filePathFactory'] = function ($container) {
            return new FilePathFactory();
        };

        $this['fieldGroupFactory'] = function ($container) {
            return new FieldGroupFactory();
        };

        $this['channelFieldFactory'] = function ($container) {
            return new ChannelFieldFactory();
        };

        $this['filePaths'] = function ($container) {
            return new FilePaths(
                $container['filePathStorage'],
                $container['filePathFactory']
            );
        };

        $this['fields'] = function ($container) {
            return new Fields(
                $container['fieldStorage'],
                $container['fieldGroupFactory'],
                $container['channelFieldFactory']
            );
        };

        $this['channelFactory'] = function ($container) {
            return new ChannelFactory();
        };

        $this['channels'] = function ($container) {
            return new Channels(
                $container['channelStorage'],
                $container['fields'],
                $container['channelFactory'],
                $container['fieldGroupFactory']
            );
        };

        $this['model'] = $this->factory(function ($container) {
            return new Model($container['db'], $container['channels'], $container['fields'], $_REQUEST);
        });

        $this['entryFieldFactory'] = function ($container) {
            return new EntryFieldFactory($container['filePaths'], $container['channelFieldFactory']);
        };

        $this['entryFieldCollectionFactory'] = function ($container) {
            return new EntryFieldCollectionFactory();
        };

        $this['entryFactory'] = function ($container) {
            return new EntryFactory($container['entryFieldFactory'], $container['entryFieldCollectionFactory']);
        };

        $this['entries'] = $this->factory(function ($container) {
            $entries = new Entries(
                $container['channels'],
                $container['model'],
                $container['db'],
                $container['entryFactory'],
                $container['entryFieldFactory'],
                $container['channelFieldFactory']
            );

            $entries->setBaseUrl($container['ee']->config->item('site_url'));

            return $entries;
        });
    }
}
