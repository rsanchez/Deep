<?php

namespace rsanchez\Deep\Entry;

use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Channel\Repository as ChannelRepository;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Factory as EntryFactory;
use rsanchez\Deep\Entry\Model;
use rsanchez\Deep\Entity\Collection as EntityCollection;
use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Channel\Field\CollectionFactory as ChannelFieldCollectionFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;

class Entries extends EntityCollection
{
    protected $model;
    protected $channelRepository;
    protected $factory;

    protected $entryIds = array();

    protected $fieldPreloaders = array();
    protected $fieldPostloaders = array();
    protected $baseUrl = '/';

    public function __construct(
        ChannelRepository $channelRepository,
        Model $model,
        DbInterface $db,
        EntryFactory $factory,
        ChannelFieldCollectionFactory $channelFieldCollectionFactory,
        FieldtypeCollectionFactory $fieldtypeCollectionFactory,
        FieldtypeRepository $fieldtypeRepository
    ) {
        $this->channelRepository = $channelRepository;
        $this->model = $model;
        $this->db = $db;
        $this->factory = $factory;
        $this->fieldtypeCollectionFactory = $fieldtypeCollectionFactory;
        $this->channelFieldCollectionFactory = $channelFieldCollectionFactory;
        $this->fieldtypeRepository= $fieldtypeRepository;
    }

    public function applyParams(array $params)
    {
        foreach ($params as $key => $value) {
            if (strncmp($key, 'search:', 7) === 0) {
                $this->__call('search', array(substr($key, 7), $value));
            } else {
                $this->__call($key, array($value));
            }
        }
    }

    public function push(Entry $entry)
    {
        parent::push($entry);
    }

    public function entryIds()
    {
        return $this->entryIds;
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

            $fieldGroupsCollected = array();
            $fieldtypesCollected = array();

            $channelFields = $this->channelFieldCollectionFactory->createCollection();
            $preloadingFieldtypes = $this->fieldtypeCollectionFactory->createCollection();

            foreach ($query->result() as $row) {
                $channel = $this->channelRepository->find($row->channel_id);

                if ($channel->field_group && ! in_array($channel->field_group, $fieldGroupsCollected)) {
                    $fieldGroupsCollected[] = $channel->field_group;

                    foreach ($channel->fields as $channelField) {
                        $channelFields->push($channelField);

                        if (! in_array($channelField->field_type, $fieldtypesCollected)) {
                            $fieldtypesCollected[] = $channelField->field_type;

                            $fieldtype = $this->fieldtypeRepository->find($channelField->field_type);

                            if ($fieldtype->preload) {
                                if ($fieldtype->preloadHighPriority) {
                                    $preloadingFieldtypes->unshift($fieldtype);
                                } else {
                                    $preloadingFieldtypes->push($fieldtype);
                                }
                            }
                        }
                    }
                }

                $this->entryIds[] = $row->entry_id;

                $entry = $this->factory->createEntry($row, $channel);

                $this->push($entry);
            }

            $query->free_result();

            // pre-load any fieldtype data, eg. Matrix
            foreach ($preloadingFieldtypes as $fieldtype) {
                $fields = $channelFields->filterByType($fieldtype->name);

                $payload = $fieldtype->preload($this, $fields);

                array_walk($this->entities, function ($entry) use ($fieldtype, $fields, $payload) {
                    $fieldtype->hydrate($entry, $fields, $payload);
                });
            }
        }

        return $this;
    }

    public function valid()
    {
        $this->get();

        return parent::valid();
    }
}
