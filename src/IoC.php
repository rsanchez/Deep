<?php

namespace rsanchez\Deep;

use rsanchez\Deep\Db\Db;
use rsanchez\Deep\Channel\Factory as ChannelFactory;
use rsanchez\Deep\Channel\Field as ChannelField;
use rsanchez\Deep\Channel\Field\Group as FieldGroup;
use rsanchez\Deep\Row\Factory as RowFactory;
use rsanchez\Deep\Row\CollectionFactory as RowCollectionFactory;
use rsanchez\Deep\FilePath\FilePath;
use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use rsanchez\Deep\FilePath\Factory as FilePathFactory;
use rsanchez\Deep\FilePath\Storage as FilePathStorage;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Fieldtype\Factory as FieldtypeFactory;
use rsanchez\Deep\Fieldtype\Storage as FieldtypeStorage;
use rsanchez\Deep\Channel\Field\GroupFactory as FieldGroupFactory;
use rsanchez\Deep\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Deep\Channel\Field\Repository as ChannelFieldRepository;
use rsanchez\Deep\Channel\Field\CollectionFactory as ChannelFieldCollectionFactory;
use rsanchez\Deep\Channel\Repository as ChannelRepository;
use rsanchez\Deep\Channel\Storage as ChannelStorage;
use rsanchez\Deep\Channel\Field\Storage as FieldStorage;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Entry\Model;
use rsanchez\Deep\Entry\Factory as EntryFactory;
use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Fieldtype\Date as DateFieldtype;
use rsanchez\Deep\Fieldtype\File as FileFieldtype;
use rsanchez\Deep\Fieldtype\FileGenerator as FileFieldtypeGenerator;
use rsanchez\Deep\Fieldtype\Matrix as MatrixFieldtype;
use rsanchez\Deep\Fieldtype\MatrixGenerator as MatrixFieldtypeGenerator;
use rsanchez\Deep\Fieldtype\Storage\Matrix as MatrixStorage;
use rsanchez\Deep\Col\Storage\Matrix as MatrixColStorage;
use rsanchez\Deep\Col\Storage\Grid as GridColStorage;
use rsanchez\Deep\Col\Repository\Matrix as MatrixColRepository;
use rsanchez\Deep\Col\Repository\Grid as GridColRepository;

use Pimple;
use stdClass;

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

        $this['fieldtypeStorage'] = function ($container) {
            return new FieldtypeStorage($container['db']);
        };

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

        $this['fieldtypeFactory'] = function ($container) {
            return new FieldtypeFactory();
        };

        $this['fieldGroupFactory'] = function ($container) {
            return new FieldGroupFactory();
        };

        $this['channelFieldCollectionFactory'] = function ($container) {
            return new ChannelFieldCollectionFactory();
        };

        $this['fieldtypeRepository'] = function ($container) {
            return new FieldtypeRepository(
                $container['fieldtypeStorage'],
                $container['fieldtypeFactory']
            );
        };

        $this['fieldtypeCollectionFactory'] = function ($container) {
            return new FieldtypeCollectionFactory();
        };

        $this['colFactory'] = function ($container) {
            return new ColFactory($container['fieldtypeRepository']);
        };

        $this['channelFieldFactory'] = function ($container) {
            return new ChannelFieldFactory($container['fieldtypeRepository']);
        };

        $this['filePathRepository'] = function ($container) {
            return new FilePathRepository(
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

        $this['rowFactory'] = function ($container) {
            return new RowFactory();
        };

        $this['rowCollectionFactory'] = function ($container) {
            return new RowCollectionFactory();
        };

        $this['entryFactory'] = function ($container) {
            return new EntryFactory();
        };

        $this['entries'] = $this->factory(function ($container) {
            return new Entries(
                $container['channelRepository'],
                $container['entryFactory'],
                $container['channelFieldCollectionFactory'],
                $container['fieldtypeCollectionFactory'],
                $container['fieldtypeRepository'],
                $container['model']
            );
        });

        $this['matrixStorage'] = function ($container) {
            return new MatrixStorage($container['db']);
        };

        $this['matrixColStorage'] = function ($container) {
            return new MatrixColStorage($container['db']);
        };

        $this['gridColStorage'] = function ($container) {
            return new GridColStorage($container['db']);
        };

        $this['matrixColRepository'] = function ($container) {
            return new MatrixColRepository($container['matrixColStorage'], $container['colFactory']);
        };

        $this['gridColRepository'] = function ($container) {
            return new GridColRepository($container['gridColStorage'], $container['colFactory']);
        };

        $this['fileFieldtypeGenerator'] = function ($container) {
            return new FileFieldtypeGenerator($container['filePathRepository']);
        };

        $this['matrixFieldtypeGenerator'] = function ($container) {
            return new MatrixFieldtypeGenerator(
                $container['fieldtypeRepository'],
                $container['matrixColRepository'],
                $container['matrixStorage'],
                $container['rowFactory'],
                $container['rowCollectionFactory']
            );
        };
        
        $this['fieldtypeRepository']->registerFieldtype('matrix', $this['matrixFieldtypeGenerator']);
        $this['fieldtypeRepository']->registerFieldtype('file', $this['fileFieldtypeGenerator']);
        $this['fieldtypeRepository']->registerFieldtype('date', '\\rsanchez\\Deep\\Fieldtype\\Date');
        Entry::$baseUrl = $this['ee']->config->item('site_url');
    }
}
