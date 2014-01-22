<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\Factory;
use \rsanchez\Entries\ChannelsInterface;
use \rsanchez\Entries\Entries\Entry;
use \rsanchez\Entries\Entries\Query as EntriesQuery;
use \rsanchez\Entries\Entries\Collection;
use \rsanchez\Entries\DbInterface;

class Entries extends Collection
{
    public $authorId = array();
    public $cache = false;
    public $refresh = 60;
    public $catLimit;
    public $category = array();
    public $categoryGroup = array();
    public $channelId = array();
    public $disable = array();
    public $displayBy;
    public $dynamic = true;
    public $dynamicParameters;
    public $dynamicStart;
    public $entryId = array();
    public $notEntryId = array();
    public $entryIdFrom;
    public $entryIdTo;
    public $fixedOrder = array();
    public $groupId = array();
    public $not_groupId = array();
    public $limit;
    public $monthLimit;
    public $offset;
    public $orderby = array('entry_date');
    public $paginate;
    public $paginateBase;
    public $paginateType;
    public $relatedCategoriesMode;
    public $relaxedCategories;
    public $requireEntry;
    public $search;
    public $showCurrentWeek = false;
    public $showExpired = false;
    public $showFutureEntries = false;
    public $showPages;
    public $sort = 'desc';
    public $startDay;
    public $startOn;
    public $status = 'open';
    public $stopBefore;
    public $sticky = false;
    public $trackViews;
    public $uncategorizedEntries = true;
    public $urlTitle;
    public $username;
    public $weekSort = 'desc';
    public $year;
    public $month;
    public $day;

    protected $query;
    protected $channels;
    protected $factory;
    protected $dynamicParameters;

    public function __construct(Channels $channels, EntriesQuery $query, EntryFactory $factory, $dynamicParameters = array())
    {
        $this->channels = $channels;
        $this->query = $query;
        $this->factory = $factory;
        $this->dynamicParameters = $dynamicParameters;
    }

    public function get()
    {
        static $executed = false;

        if (! $executed) {

            $order = array(
                //'channel',
                'channelId',
                'dynamicParameters',
                'disable',
                'authorId',
                //'cache',
                //'refresh',
                'catLimit',
                'category',
                'categoryGroup',
                'displayBy',
                'dynamic',
                'sticky',
                'entryId',
                //@TODO//'notEntryId',
                'entryIdFrom',
                'entryIdTo',
                'fixedOrder',
                'groupId',
                //@TODO//'notGroupId',
                'limit',
                'monthLimit',
                'offset',
                'orderby',
                'requireEntry',
                'search',
                'showCurrentWeek',
                'showExpired',
                'showFutureEntries',
                'sort',
                'startDay',
                'startOn',
                'status',
                'stopBefore',
                'uncategorizedEntries',
                'urlTitle',
                'username',
                'weekSort',
                'year',
                'month',
                'day',
            );

            foreach ($order as $which) {
                if (! is_null($this->$which)) {
                    $this->query->$which($this->$which);
                }
            }

            $executed = true;

            foreach ($this->query->result() as $row) {
                $this->push($this->factory($row, $this->channels->find($row->channel_id)));
            }
        }

        return $this;
    }

    public function valid()
    {
        $this->get();

        return parent::valid();
    }

    protected function setIntegerParam(&$property, $value)
    {
        if (is_int($value)) {
            $property = $value;
        } elseif (ctype_digit($value)) {
            $property = intval($value);
        }

        return $this;
    }

    protected function setBoolParam(&$property, $value)
    {
        $property = (bool) $value;

        return $this;
    }

    protected function setStringParam(&$property, $value)
    {
        $property = strval($value);

        return $this;
    }

    protected function setArrayParam(&$property, $value)
    {
        if (! is_array($value)) {
            if (func_num_args() > 2) {
                $property = array_slice(func_get_args(), 1);
            } else {
                $property = explode('|', strval($value));
            }
        } else {
            $property = array();

            foreach ($value as $v) {
                $this->{$name}[] = strval($v);
            }
        }

        return $this;
    }

    protected function setArrayIntegerParam(&$property, $value)
    {
        if (! is_array($value)) {
            if (func_num_args() > 2) {
                $value = array_slice(func_get_args(), 1);
            } else {
                $value = explode('|', $value);
            }
        }

        $property = array();
        
        foreach ($value as $v) {
            if (ctype_digit($v)) {
                $this->{$name}[] = intval($v);
            }
        }

        return $this;
    }

    protected function setDateParam(&$property, $value)
    {
        if (ctype_digit($value)) {
            $property = intval($value);
        } elseif (($value = strtotime($value)) !== false) {
            $property = $value;
        }

        return $this;
    }

    protected function setRegexParam(&$property, $value, $pattern)
    {
        if (preg_match($pattern, $value)) {
            $property = $value;
        }

        return $this;
    }

    public function channel($value)
    {
        $channelId = array();

        if (! is_array($value)) {
            $value = array($value);
        }

        foreach ($value as $channelName) {
            try {
                $channel = $this->channels->find($channelName);

                $channelId[] = $channel->channel_id;
            } catch(Exception $e) {
                $e->getMessage();
            }
        }

        $this->setArrayParam($this->channelId, $channelId);

        return $this;
    }

    public function channel_id($value)
    {
        $this->setArrayIntegerParam($this->channelId, $value);

        return $this;
    }

    public function author_id($value)
    {
        $this->setArrayIntegerParam($this->authorId, $value);

        return $this;
    }

    public function cat_limit($value)
    {
        $this->setIntegerParam($this->catLimit, $value);

        return $this;
    }

    public function category($value)
    {
        $this->setArrayIntegerParam($this->category, $value);

        return $this;
    }

    public function category_group($value)
    {
        $this->setArrayIntegerParam($this->categoryGroup, $value);

        return $this;
    }

    public function display_by($value)
    {
        $this->setRegexParam($this->displayBy, $value, '/^month|day|number$/i');

        return $this;
    }

    public function dynamic($value)
    {
        $this->setBoolParam($this->dynamic, $value);

        return $this;
    }

    public function sticky($value)
    {
        $this->setBoolParam($this->sticky, $value);

        return $this;
    }

    public function entry_id($value)
    {
        $this->setArrayIntegerParam($this->entryId, $value);

        return $this;
    }

    public function not_entry_id($value)
    {
        $this->setArrayIntegerParam($this->notEntryId, $value);

        return $this;
    }

    public function entry_id_from($value)
    {
        $this->setIntegerParam($this->entryIdFrom, $value);

        return $this;
    }

    public function entry_id_to($value)
    {
        $this->setIntegerParam($this->entryIdTo, $value);

        return $this;
    }

    public function fixed_order($value)
    {
        $this->setArrayIntegerParam($this->fixedOrder, $value);

        return $this;
    }

    public function group_id($value)
    {
        $this->setArrayIntegerParam($this->groupId, $value);

        return $this;
    }

    public function not_group_id($value)
    {
        $this->setArrayIntegerParam($this->notGroupId, $value);

        return $this;
    }

    public function limit($value)
    {
        $this->setIntegerParam($this->limit, $value);

        return $this;
    }

    public function month_limit($value)
    {
        $this->setIntegerParam($this->monthLimit, $value);

        return $this;
    }

    public function offset($value)
    {
        $this->setIntegerParam($this->offset, $value);

        return $this;
    }

    public function orderby($value)
    {
        $this->setStringParam($this->orderby, $value);

        return $this;
    }

    public function require_entry($value)
    {
        $this->setBoolParam($this->requireEntry, $value);

        return $this;
    }

    public function search($field, $value)
    {


        return $this;
    }

    public function show_current_week($value)
    {
        $this->setBoolParam($this->showCurrentWeek, $value);

        return $this;

    }

    public function show_expired($value)
    {
        $this->setBoolParam($this->showExpired, $value);

        return $this;
    }

    public function show_future_entries($value)
    {
        $this->setBoolParam($this->showFutureEntries, $value);

        return $this;
    }

    public function sort($value)
    {
        $this->setRegexParam($this->sort, $value, '/^asc|desc$/i');

        return $this;
    }

    public function start_day($value)
    {
        $this->setRegexParam($this->startDay, $value, '/^Monday|Sunday$/i');

        return $this;
    }

    public function start_on($value)
    {
        $this->setDateParam($this->startOn, $value);

        return $this;
    }

    public function status($value)
    {
        $this->setStringParam($this->status, $value);

        return $this;
    }

    public function stop_before($value)
    {
        $this->setDateParam($this->stopBefore, $value);

        return $this;
    }

    public function uncategorized_entries($value)
    {
        $this->setBoolParam($this->uncategorizedEntries, $value);

        return $this;
    }

    public function url_title($value)
    {
        $this->setStringParam($this->urlTitle, $value);

        return $this;
    }

    public function username($value)
    {
        $this->setStringParam($this->username, $value);

        return $this;
    }

    public function week_sort($value)
    {
        $this->setRegexParam($this->weekSort, $value, '/^asc|desc$/i');

        return $this;
    }

    public function year($value)
    {
        $this->setIntegerParam($this->year, $value);

        return $this;
    }

    public function month($value)
    {
        $this->setIntegerParam($this->month, $value);

        return $this;
    }

    public function day($value)
    {
        $this->setIntegerParam($this->day, $value);

        return $this;
    }
}
