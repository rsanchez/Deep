<?php

namespace rsanchez\Deep;

use rsanchez\Deep\Db\Db;
use rsanchez\Deep\Channel\Factory as ChannelFactory;
use rsanchez\Deep\Channel\Field as ChannelField;
use rsanchez\Deep\Channel\Field\Group as FieldGroup;
use rsanchez\Deep\FilePath\FilePath;
use rsanchez\Deep\FilePath\Repository;
use rsanchez\Deep\FilePath\Factory as FilePathFactory;
use rsanchez\Deep\FilePath\Storage as FilePathStorage;
use rsanchez\Deep\Entity\Field\CollectionFactory as EntityFieldCollectionFactory;
use rsanchez\Deep\Channel\Field\GroupFactory as FieldGroupFactory;
use rsanchez\Deep\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Deep\Channel\Field\Repository as ChannelFieldRepository;
use rsanchez\Deep\Channel\Repository as ChannelRepository;
use rsanchez\Deep\Channel\Storage as ChannelStorage;
use rsanchez\Deep\Channel\Field\Storage as FieldStorage;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Entity\Field as EntityField;
use rsanchez\Deep\Entry\Model;
use rsanchez\Deep\Entity\Field\Factory as EntityFieldFactory;
use rsanchez\Deep\Entry\Factory as EntryFactory;
use Pimple;

class IoC extends Pimple
{
    public function __construct()
    {
        parent::__construct();

        $this['ee'] = function ($container) {
            return ee();
        };

        $this['db'] = $this->factory(function ($container) {
            static $count = 1;

            $db = new Db(array(
                'dbdriver' => 'mysql',
                'conn_id'  => $container['ee']->db->conn_id,
                'database' => $container['ee']->db->database,
                'dbprefix' => $container['ee']->db->dbprefix,
            ));

            // log queries
            $db->save_queries = $container['ee']->config->item('show_profiler') === 'y' || DEBUG === 1;

            // attach back to ee so the profiler knows to show these queries
            $container['ee']->{'db'.$count} = $db;

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

        $this['colFactory'] = function ($container) {
            return new ColFactory();
        };

        $this['filePathRepository'] = function ($container) {
            return new Repository(
                $container['filePathStorage'],
                $container['filePathFactory']
            );
        };

        $this['channelFieldRepository'] = function ($container) {
            return new ChannelFieldRepository(
                $container['fieldStorage'],
                $container['fieldGroupFactory'],
                $container['channelFieldFactory']
            );
        };

        $this['channelFactory'] = function ($container) {
            return new ChannelFactory();
        };

        $this['channelRepository'] = function ($container) {
            return new ChannelRepository(
                $container['channelStorage'],
                $container['channelFieldRepository'],
                $container['channelFactory'],
                $container['fieldGroupFactory']
            );
        };

        $this['model'] = $this->factory(function ($container) {
            return new Model($container['db'], $container['channelRepository'], $container['channelFieldRepository'], $_REQUEST);
        });

        $this['entryFieldFactory'] = function ($container) {
            return new EntityFieldFactory($container['filePathRepository'], $container['colFactory']);
        };

        $this['entryFieldCollectionFactory'] = function ($container) {
            return new EntityFieldCollectionFactory();
        };

        $this['entryFactory'] = function ($container) {
            return new EntryFactory($container['entryFieldFactory'], $container['entryFieldCollectionFactory']);
        };

        $this['entries'] = $this->factory(function ($container) {
            $entries = new Entries(
                $container['channelRepository'],
                $container['model'],
                $container['db'],
                $container['entryFactory']
            );

            $entries->setBaseUrl($container['ee']->config->item('site_url'));

            return $entries;
        });
    }
}
