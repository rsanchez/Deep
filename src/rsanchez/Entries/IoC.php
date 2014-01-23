<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Db;
use rsanchez\Entries\Channel\Factory as ChannelFactory;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Channel\Field\Group as FieldGroup;
use rsanchez\Entries\Channel\Storage as ChannelStorage;
use rsanchez\Entries\Channel\Field\Storage as FieldStorage;
use rsanchez\Entries\Entries\Entry;
use rsanchez\Entries\Entries\Field as EntryField;
use rsanchez\Entries\Entries\Filter;
use rsanchez\Entries\Entries\Query as EntryQuery;
use \Pimple;

class IoC extends Pimple
{
    public function __construct()
    {
        $this['db'] = $this->factory(function () {
            return new Db(array(
                'dbdriver' => 'mysql',
                'conn_id'  => ee()->db->conn_id,
                'database' => ee()->db->database,
                'dbprefix' => ee()->db->dbprefix,
            ));
        });

        $this['fieldStorage'] = function ($container) {
            return new FieldStorage($container['db']);
        };

        $this['channelStorage'] = function ($container) {
            return new ChannelStorage($container['db']);
        };

        $this['fieldGroupFactory'] = function ($container) {
            return new FieldGroupFactory();
        };

        $this['channelFieldFactory'] = function ($container) {
            return new ChannelFieldFactory();
        };

        $this['fieldGroups'] = function ($container) {
            return new FieldGroups(
                $container['fieldStorage'],
                $container['fieldGroupFactory'],
                $container['channelFieldFactory']
            );
        };

        $this['channelFactory'] =function ($container) {
            return new ChannelFactory();
        };

        $this['channels'] = function ($container) {
            return new Channels(
                $container['channelStorage'],
                $container['fieldGroups'],
                $container['channelFactory'],
                $container['fieldGroupFactory']
            );
        };

        $this['entryQuery'] = $this->factory(function ($container) {
            return new EntryQuery($container['db'], $_REQUEST);
        });

        $this['filter'] = $this->factory(function ($container) {
            return new Filter();
        });

        $this['entryField'];

        $this['entryFactory'];

        $this['entries'] = $container->factory(function ($container) {
            return new Entries($container['db'], $container['channels'], $container['entryFactory']);
        });
    }
}
