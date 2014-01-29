<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\ChannelsInterface;
use \rsanchez\Entries\Entry;
use \rsanchez\Entries\Entry\Factory as EntryFactory;
use \rsanchez\Entries\Channel\Field\Factory as ChannelFieldFactory;
use \rsanchez\Entries\Entity\Field\Factory as EntityFieldFactory;
use \rsanchez\Entries\Entity\Field as EntityField;
use \rsanchez\Entries\Model;
use \rsanchez\Entries\EntityCollection;
use \rsanchez\Entries\DbInterface;

class Entries extends EntityCollection
{
    protected $model;
    protected $channels;
    protected $factory;

    protected $entryIds = array();

    protected $fieldPreloaders = array();
    protected $fieldPostloaders = array();
    protected $baseUrl = '/';

    public function __construct(
        Channels $channels,
        Model $model,
        DbInterface $db,
        EntryFactory $factory,
        EntityFieldFactory $entryFieldFactory,
        ChannelFieldFactory $channelFieldFactory
    ) {
        $this->channels = $channels;
        $this->model = $model;
        $this->db = $db;
        $this->factory = $factory;
        $this->entryFieldFactory = $entryFieldFactory;
        $this->channelFieldFactory = $channelFieldFactory;
    }

    public function push(Entry $entry)
    {
        parent::push($entry);
    }

    /**
     * Register Field Preloader
     *
     * Some field types store data in their own DB table(s),
     * e.g. Matrix, Grid, etc.
     *
     * This allows you to add a callback where your fieldtype
     * can query the database for additional data. The callback
     * is called after the entries are loaded, and is only called
     * ONCE per namespace. In your callback, you should load
     * all of the fieldtype
     * @param  [type] $namespace [description]
     * @param  [type] $callback  [description]
     * @return [type]            [description]
     */
    public function registerFieldPreloader($fieldType, EntityField $entryField, $highPriority = false)
    {
        // preload only once
        if (! array_key_exists($fieldType, $this->fieldPreloaders)) {
            if ($highPriority) {
                $this->fieldPreloaders = array($fieldType => $entryField) + $this->fieldPreloaders;
            } else {
                $this->fieldPreloaders[$fieldType] = $entryField;
            }
        }

        // postload each field
        if (! isset($this->fieldPostloaders[$fieldType])) {
            $this->fieldPostloaders[$fieldType] = array();
        }

        $this->fieldPostloaders[$fieldType][] = $entryField;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function entryIds()
    {
        return $this->entryIds;
    }

    public function baseUrl()
    {
        if (is_null($this->baseUrl)) {
            throw new \Exception('You must set a baseUrl: Entries::setBaseUrl("http://yoursite.com/")');
        }

        return $this->baseUrl;
    }

    public function __call($name, $args)
    {
        static $methodMap = array(
            'author_id' => 'authorId',
            'cat_limit' => 'catLimit',
            'category_group' => 'categoryGroup',
            'channel_id' => 'channelId',
            'display_by' => 'displayBy',
            'dynamic' => 'dynamic',
            'dynamic_parameters' => 'dynamicParameters',
            'dynamic_start' => 'dynamicStart',
            'entry_id' => 'entryId',
            'not_entry_id' => 'notEntryId',
            'entry_id_from' => 'entryIdFrom',
            'entry_id_fo' => 'entryIdTo',
            'fixed_order' => 'fixedOrder',
            'group_id' => 'groupId',
            'not_group_id' => 'notGroupId',
            'month_limit' => 'monthLimit',
            'paginate_base' => 'paginateBase',
            'paginate_type' => 'paginateType',
            'related_categories_mode' => 'relatedCategoriesMode',
            'relaxed_categories' => 'relaxedCategories',
            'require_entry' => 'requireEntry',
            'show_current_week' => 'showCurrentWeek',
            'show_expired' => 'showExpired',
            'show_future_entries' => 'showFutureEntries',
            'show_pages' => 'showPages',
            'start_day' => 'startDay',
            'start_on' => 'startOn',
            'stop_before' => 'stopBefore',
            'track_views' => 'trackViews',
            'uncategorized_entries' => 'uncategorizedEntries',
            'url_title' => 'urlTitle',
            'week_sort' => 'weekSort',
        );

        if (array_key_exists($name, $methodMap)) {
            $name = $methodMap[$name];
        }

        if (method_exists($this->model, $name) && is_callable(array($this->model, $name))) {
            return call_user_func_array(array($this->model, $name), $args);
        }

        throw new \Exception('invalid method '.$name);
    }

    public function get()
    {
        static $executed = false;

        if (! $executed) {

            $query = $this->model->get();

            $executed = true;

            foreach ($query->result() as $row) {
                $this->entryIds[] = $row->entry_id;

                $entry = $this->factory->createEntry($row, $this, $this->channels->find($row->channel_id));

                $this->push($entry);
            }

            $query->free_result();

            $payloads = array();

            // pre-load any fieldtype data, eg. Matrix
            foreach ($this->fieldPreloaders as $fieldType => $entryField) {
                $fieldIds = $this->channels->fields->filterByType($fieldType)->fieldIds();
                $payloads[$fieldType] = $entryField->preload($this->db, $this->entryIds, $fieldIds);
            }

            foreach ($this->fieldPostloaders as $fieldType => $entryFields) {
                foreach ($entryFields as $entryField) {
                    $entryField->hydrate($payloads[$fieldType]);
                }
            }

            unset($payloads);
        }

        return $this;
    }

    public function valid()
    {
        $this->get();

        return parent::valid();
    }
}
