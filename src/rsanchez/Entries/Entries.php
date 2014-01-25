<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\ChannelsInterface;
use \rsanchez\Entries\Entries\Entry;
use \rsanchez\Entries\Entries\Factory as EntryFactory;
use \rsanchez\Entries\Entries\Field\Factory as FieldFactory;
use \rsanchez\Entries\Entries\Model as EntriesModel;
use \rsanchez\Entries\Entries\Collection;
use \rsanchez\Entries\DbInterface;

class Entries extends Collection
{
    protected $model;
    protected $channels;
    protected $factory;

    public function __construct(Channels $channels, EntriesModel $model, EntryFactory $factory, FieldFactory $fieldFactory)
    {
        $this->channels = $channels;
        $this->model = $model;
        $this->factory = $factory;
        $this->fieldFactory = $fieldFactory;
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
                $this->push(
                    $this->factory->createEntry(
                        $this->channels->find($row->channel_id),
                        $this->fieldFactory,
                        $row
                    )
                );
            }

            $query->free_result();
        }

        return $this;
    }

    public function valid()
    {
        $this->get();

        return parent::valid();
    }
}
