<?php

namespace rsanchez\Deep\Entry;

use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Channel\Repository as ChannelRepository;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Factory as EntryFactory;
use rsanchez\Deep\Entry\Model;
use rsanchez\Deep\Entity\AbstractCollection as EntityCollection;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Channel\Field\CollectionFactory as FieldCollectionFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use SplObjectStorage;

class Entries extends EntityCollection
{
    protected $model;
    protected $channelRepository;
    protected $factory;

    protected $entryIds;

    protected $fieldPreloaders = array();
    protected $fieldPostloaders = array();
    protected $baseUrl = '/';

    public function __construct(
        EntryFactory $factory,
        FieldtypeRepository $fieldtypeRepository,
        FieldtypeCollectionFactory $fieldtypeCollectionFactory,
        FieldCollectionFactory $fieldCollectionFactory,
        ChannelRepository $channelRepository,
        Model $model
    ) {
        parent::__construct($factory, $fieldtypeRepository, $fieldtypeCollectionFactory, $fieldCollectionFactory);

        $this->channelRepository = $channelRepository;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->model = $model;

        $this->entryIds =& $this->entityIds;
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

    public function attach(Entry $entry)
    {
        parent::attach($entry);
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

            $this->fill($query->result());

            $query->free_result();
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fill(array $result)
    {
        $fieldGroupsCollected = array();
        $fieldtypesCollected = array();

        $fields = $this->fieldCollectionFactory->createCollection();
        $preloadingFieldtypes = $this->fieldtypeCollectionFactory->createCollection();

        $entries = new SplObjectStorage();

        foreach ($result as $row) {
            $channel = $this->channelRepository->find($row->channel_id);

            if ($channel->field_group && ! in_array($channel->field_group, $fieldGroupsCollected)) {
                $fieldGroupsCollected[] = $channel->field_group;

                foreach ($channel->fields as $field) {
                    $fields->attach($field);

                    if (! in_array($field->field_type, $fieldtypesCollected)) {
                        $fieldtypesCollected[] = $field->field_type;

                        $fieldtype = $this->fieldtypeRepository->find($field->field_type);

                        if ($fieldtype->preload) {
                            if ($fieldtype->preloadHighPriority) {
                                $preloadingFieldtypes->unshift($fieldtype);
                            } else {
                                $preloadingFieldtypes->attach($fieldtype);
                            }
                        }
                    }
                }
            }

            $entry = $this->factory->createEntry($row, $channel);

            $this->entryIds[] = $entry->entry_id;

            $entries->attach($entry);
        }

        // pre-load any fieldtype data, eg. Matrix
        foreach ($preloadingFieldtypes as $fieldtype) {
            $fields = $fields->filterByType($fieldtype->name);

            $payload = $fieldtype->preload($this, $fields);

            foreach($entries as $entry) {
                $fieldtype->hydrate($entry, $fields, $payload);
            }
        }

        $this->addAll($entries);
    }

    public function valid()
    {
        $this->get();

        return parent::valid();
    }

    public function rewind()
    {
        $this->get();

        return parent::rewind();
    }
}
