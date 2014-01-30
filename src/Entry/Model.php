<?php

namespace rsanchez\Deep\Entry;

use rsanchez\Deep\Channel\Repository as ChannelRepository;
use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Channel\Field\Repository as ChannelFieldRepository;

class Model
{
    protected $authorId = array();
    protected $cache = false;
    protected $refresh = 60;
    protected $catLimit;
    protected $category = array();
    protected $categoryGroup = array();
    protected $channel = array();
    protected $channelId = array();
    protected $disable = array('member_data', 'pagination');
    protected $displayBy;
    protected $dynamic = true;
    protected $dynamicParameters;
    protected $dynamicStart;
    protected $entryId = array();
    protected $notEntryId = array();
    protected $entryIdFrom;
    protected $entryIdTo;
    protected $fixedOrder = array();
    protected $groupId = array();
    protected $notGroupId = array();
    protected $limit;
    protected $monthLimit;
    protected $offset;
    protected $orderby = array('entry_date');
    protected $paginate;
    protected $paginateBase;
    protected $paginateType;
    protected $relatedCategoriesMode;
    protected $relaxedCategories;
    protected $requireEntry;
    protected $search = array();
    protected $showCurrentWeek = false;
    protected $showExpired = false;
    protected $showFutureEntries = false;
    protected $showPages;
    protected $sort = array('DESC');
    protected $startDay;
    protected $startOn;
    protected $status = array('open');
    protected $notStatus = array();
    protected $stopBefore;
    protected $sticky = false;
    protected $trackViews;
    protected $uncategorizedEntries = true;
    protected $urlTitle;
    protected $username;
    protected $weekSort = 'desc';
    protected $year;
    protected $month;
    protected $day;

    protected $db;
    protected $channelRepository;
    protected $request;
    protected $channelFieldRepository;

    public function __construct(DbInterface $db, ChannelRepository $channelRepository, ChannelFieldRepository $channelFieldRepository, $request = array())
    {
        $this->db = $db;
        $this->channelFieldRepository = $channelFieldRepository;
        $this->channelRepository = $channelRepository;
        $this->request = $request;
    }

    public function __call($name, $args)
    {
        $object = is_callable(array($this->db, $name)) ? $this->db : $this;

        return call_user_func_array(array($object, $name), $args);
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
                $property[] = strval($v);
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
                $property[] = intval($v);
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

    protected function setArrayRegexParam(&$property, $value, $pattern, $default = null)
    {
        if (! is_array($value)) {
            $value = explode('|', $value);
        }

        foreach ($value as $v) {
            if (preg_match($pattern, $v)) {
                $property[] = $v;
            } elseif ($default !== null) {
                $property[] = $default;
            }
        }

        return $this;
    }

    protected function requireTable($which)
    {
        static $joined = array();

        $tables = array(
            'members' => 'members.member_id = channel_titles.author_id',
            'channel_data' => 'channel_data.entry_id = channel_titles.entry_id',
            'channels' => 'channels.channel_id = channel_titles.channel_id',
        );
        
        if (in_array($which, $joined) || ! isset($tables[$which])) {
            return;
        }
        
        $this->db->join($which, $tables[$which]);
        
        $joined[] = $which;
    }

    public function get()
    {
        /**
         * Start a-queryin'
         **/
        $this->db->select('channel_titles.*');

        $this->db->from('channel_titles');

        /**
         * Channel and Channel ID
         * 
         * loop through the requested channel namess and
         * convert to ID, avoiding a join with the channels table
         */
        $channelId = $this->channelId;

        foreach ($this->channel as $channelName) {
            try {
                $channel = $this->channelRepository->find($channelName);

                $channelId[] = $channel->channel_id;
            } catch (Exception $e) {
                //$e->getMessage();
            }
        }

        if ($channelId) {
            $this->db->where_in('channel_titles.channel_id', $channelId);
        }

        /**
         * Custom Fields
         */
        if (! in_array('custom_fields', $this->disable)) {
            $this->requireTable('channel_data');

            $this->db->select('channel_data.*');
        }
        
        /**
         * Member Data
         */
        if (! in_array('member_data', $this->disable)) {
            $this->requireTable('members');

            $this->db->select('members.*');
        }

        /**
         * Status
         */
        $this->db->where_in('channel_titles.status', $this->status);

        /**
         * Author ID
         */
        if ($this->authorId) {
            $this->db->where_in('channel_titles.author_id', $this->authorId);
        }

        /**
         * Expired Entries
         */
        if ($this->showExpired !== true) {
            $this->db->where(
                "(`".$this->db->dbprefix('channel_titles')."`.`expiration_date` = '' OR  `".$this->db->dbprefix('channel_titles')."`.`expiration_date` > NOW())",
                null,
                false
            );
        }

        /**
         * Future Entries
         */
        if ($this->showFutureEntries !== true) {
            $this->db->where('channel_titles.entry_date <=', time());
        }

        /**
         * Fixed Order
         */
        if ($this->fixedOrder) {
            $this->entryIds = $this->fixedOrder;

            $this->db->where_in('channel_titles.entry_id', $entryIds);

            $this->db->order_by('FIELD('.implode(', ', $entryIds).')', 'ASC', false);
        } else {
            /**
             * Sticky
             */
            if ($this->sticky === true) {
                array_unshift($this->orderby, 'channel_titles.sticky');
            }

            /**
             * Order By
             */
            foreach ($this->orderby as $i => $order_by) {
                $sort = isset($this->sort[$i]) ? $this->sort[$i] : '';
                $this->db->order_by($order_by, $sort);
            }
        }

        /**
         * Entry ID
         */
        if ($this->entryId) {
            $this->db->where_in('channel_titles.entry_id', $this->entryId);
        }

        /**
         * Not Entry ID
         */
        if ($this->notEntryId) {
            $this->db->where_not_in('channel_titles.entry_id', $this->notEntryId);
        }

        /**
         * Entry ID From
         */
        if ($this->entryIdFrom) {
            $this->db->where('channel_titles.entry_id >=', $this->entryIdFrom);
        }

        /**
         * Entry ID To
         */
        if ($this->entryIdTo) {
            $this->db->where('channel_titles.entry_id <=', $this->entryIdTo);
        }

        /**
         * Member Group ID
         */
        if ($this->groupId) {
            $this->requireTable('members');
        
            $this->db->where_in('members.group_id', $this->groupId);
        }

        /**
         * Not Member Group ID
         */
        if ($this->notGroupId) {
            $this->requireTable('members');

            $this->db->where_not_in('members.group_id', $this->notGroupId);
        }

        /**
         * Limit
         */
        if ($this->limit) {
            $this->db->limit($this->limit);
        }

        /**
         * Offset
         */
        if ($this->offset) {
            $this->db->offset($this->offset);
        }

        /**
         * Start On
         */
        if ($this->startOn) {
            $this->db->where('channel_titles.entry_date >=', $this->startOn);
        }

        /**
         * Stop Before
         */
        if ($this->stopBefore) {
            $this->db->where('channel_titles.entry_date <', $this->stopBefore);
        }

        /**
         * URL Title
         */
        if ($this->urlTitle) {
            $this->db->where_in('channel_titles.url_title', $this->urlTitle);
        }

        /**
         * Username
         */
        if ($this->username) {
            $this->requireTable('members');
            
            $this->db->where('members.username', $this->username);
        }

        /**
         * Search
         */
        if ($this->search) {
            $this->requireTable('channel_data');
            
            foreach ($this->search as $fieldName => $values) {
                try {
                    $field = $this->channelFieldRepository->find($fieldName);

                    $glue = 'OR';
                        
                    foreach ($values as $value) {
                        $query = "`".$this->db->dbprefix('channel_data')."`.`field_id_{$field->id()}` ";
                        
                        $query .= "LIKE ".$this->db->escape('%'.$value.'%');
                        
                        $queries[] = $query;
                    }
                    
                    $this->db->where('('.implode($glue, $queries).')', null, false);

                } catch (Exception $e) {
                    //$e->getMessage();
                }
            }
        }

        /**
         * Year
         */
        if ($this->year) {
            $this->db->where('channel_titles.year', $this->year);
        }

        /**
         * Month
         */
        if ($this->month) {
            $this->db->where('channel_titles.month', $this->month);
        }

        /**
         * Day
         */
        if ($this->day) {
            $this->db->where('channel_titles.day', $this->day);
        }

        return $this->db->get();
    }

    /**
     * Filter by Author ID
     * @param  int|array $authorId one or more author IDs
     * @return this
     */
    public function authorId($authorId)
    {
        $this->setArrayIntegerParam($this->authorId, $value);

        return $this;
    }

    public function catLimit($catLimit)
    {
        $this->setIntegerParam($this->catLimit, $catLimit);

        return $this;
    }

    public function category($category)
    {
        $this->setArrayIntegerParam($this->category, $category);

        return $this;
    }

    public function categoryGroup($categoryGroup)
    {
        $this->setArrayIntegerParam($this->categoryGroup, $categoryGroup);

        return $this;
    }

    /**
     * Filter by Channel Name
     * @param  string|array $channel one or more channel names
     * @return this
     */
    public function channel($channel)
    {
        $this->setArrayParam($this->channel, $channel);

        return $this;
    }

    /**
     * Filter by Channel ID
     * @param  int|array $channelId one or more channel IDs
     * @return this
     */
    public function channelId($channelId)
    {
        $this->setArrayIntegerParam($this->channelId, $channelId);
    }

    public function disable(array $disable)
    {
        $this->disable = $disable;
    }

    public function displayBy($displayBy)
    {
        $this->setRegexParam($this->displayBy, $displayBy, '/^month|day|number$/i');

        return $this;
    }

    public function dynamic()
    {
        $this->setBoolParam($this->dynamic, $value);

        return $this;
        
        //entry_id, url_title, month/year/day
    }

    public function dynamicParameters()
    {
        if (! $this->dynamicParameters) {
            return;
        }
        
        $validParameters = array(
            'author_id'           => 'authorId',
            'cat_limit'           => 'catLimit',
            'category'            => 'category',
            'channel'             => 'channel',
            'channel_id'          => 'channelId',
            'day'                 => 'day',
            'display_by'          => 'displayBy',
            'entry_id'            => 'entryId',
            'not_entry_id'        => 'notEntryId',
            'entry_id_from'       => 'entryIdFrom',
            'entry_id_to'         => 'entryIdTo',
            'group_id'            => 'groupId',
            'limit'               => 'limit',
            'month'               => 'month',
            'month_limit'         => 'monthLimit',
            'not_entry_id'        => 'notEntryId',
            'offset'              => 'offset',
            'orderby'             => 'orderby',
            'show_expired'        => 'showExpired',
            'show_future_entries' => 'showFutureEntries',
            'sort'                => 'sort',
            'start_on'            => 'startOn',
            'status'              => 'status',
            'sticky'              => 'sticky',
            'stop_before'         => 'stopBefore',
            'username'            => 'username',
            'year'                => 'year',
            'search'              => 'search',
            'exact_search'        => 'exactSearch',
        );

        foreach ($this->dynamicParameters as $key) {
            if (array_key_exists($key, $this->request)) {
                if (strncmp($key, 'search:', 7) === 0) {
                    $this->search(substr($key, 7), $this->request[$key]);
                } elseif (array_key_exists($key, $validParameters)) {
                    $method = $validParameters[$key];
                    $this->{$method}($this->request[$key]);
                }
            }
        }
    }

    public function dynamicStart()
    {
        
    }

    /**
     * Filter by Entry ID
     * @param  int|array $entryId one or more entry IDs
     * @return this
     */
    public function entryId($entryId)
    {
        $this->setArrayIntegerParam($this->entryId, $entryId);

        return $this;
    }

    /**
     * Filter by not Entry ID
     * 
     * @param  int|array $entryId one or more entry IDs
     * @return this
     */
    public function notEntryId($notEntryId)
    {
        $this->setArrayIntegerParam($this->notEntryId, $notEntryId);

        return $this;
    }

    /**
     * Filter out entries before this ID
     * @param  int $entryIdFrom
     * @return this
     */
    public function entryIdFrom($entryIdFrom)
    {
        $this->setIntegerParam($this->entryIdFrom, $entryIdFrom);

        return $this;
    }

    /**
     * Filter out entries after this ID
     * @param  int $entryIdTo
     * @return this
     */
    public function entryIdTo($entryIdTo)
    {
        $this->setIntegerParam($this->entryIdTo, $entryIdTo);

        return $this;
    }

    /**
     * Return the results in order of the specified entry IDs
     * @param  array $fixedOrder  entry IDs, in order
     * @return this
     */
    public function fixedOrder($fixedOrder)
    {
        $this->setArrayIntegerParam($this->fixedOrder, $fixedOrder);

        return $this;
    }

    /**
     * Filter by Member Group ID
     * @param  int|array $groupId one or more member group IDs
     * @return this
     */
    public function groupId($groupId)
    {
        $this->setArrayIntegerParam($this->groupId, $groupId);

        return $this;
    }

    /**
     * Filter by not Member Group ID
     * @param  int|array $groupId one or more member group IDs
     * @return this
     */
    public function notGroupId($notGroupId)
    {
        $this->setArrayIntegerParam($this->notGroupId, $notGroupId);

        return $this;
    }

    public function limit($limit)
    {
        $this->setIntegerParam($this->limit, $value);

        return $this;
    }

    public function monthLimit($value)
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

    public function requireEntry($value)
    {
        $this->setBoolParam($this->requireEntry, $value);

        return $this;
    }

    public function exactSearch($fieldName, $exactSearch)
    {
        $this->setArrayParam($this->exactSearch[$fieldName], $exactSearch);
    }

    public function search($field, $value)
    {
        $this->setArrayParam($this->search[$fieldName], $search);

        return $this;
    }

    public function showCurrentWeek($value)
    {
        $this->setBoolParam($this->showCurrentWeek, $value);

        return $this;

    }

    /**
     * Filter out expired entries
     * @param  boolean $showExpired
     * @return this
     */
    public function showExpired($showExpired = true)
    {
        $this->setBoolParam($this->showExpired, $showExpired);

        return $this;
    }

    /**
     * Filter out future entries
     * @param  boolean $showExpired
     * @return this
     */
    public function showFutureEntries($showFutureEntries)
    {
        $this->setBoolParam($this->showFutureEntries, $showFutureEntries);

        return $this;
    }

    public function sort($value)
    {
        $this->setArrayRegexParam($this->sort, $value, '/^asc|desc$/i', 'asc');

        return $this;
    }

    public function startDay($value)
    {
        $this->setRegexParam($this->startDay, $value, '/^Monday|Sunday$/i');

        return $this;
    }

    /**
     * Exclude entries after this date
     * @param  int $stopBefore unix timestap
     * @return this
     */
    public function startOn($startOn)
    {
        $this->setDateParam($this->startOn, $startOn);

        return $this;
    }

    /**
     * Exclude entries after this date
     * @param  int $stopBefore unix timestap
     * @return this
     */
    public function stopBefore($stopBefore)
    {
        $this->setDateParam($this->stopBefore, $stopBefore);

        return $this;
    }

    public function uncategorizedEntries($value)
    {
        $this->setBoolParam($this->uncategorizedEntries, $value);

        return $this;
    }

    /**
     * Filter by URL Title
     * @param  string $urlTitle
     * @return this
     */
    public function urlTitle($urlTitle)
    {
        $this->setStringParam($this->urlTitle, $urlTitle);

        return $this;
    }

    /**
     * Filter by Author Username
     * @param  string $username
     * @return this
     */
    public function username($username)
    {
        $this->setStringParam($this->username, $username);

        return $this;
    }

    public function weekSort($value)
    {
        $this->setRegexParam($this->weekSort, $value, '/^asc|desc$/i');

        return $this;
    }

    /**
     * Filter by Year
     * @param  int $year
     * @return this
     */
    public function year($year)
    {
        $this->setIntegerParam($this->year, $year);

        return $this;
    }

    /**
     * Filter by Month
     * @param  int $month
     * @return this
     */
    public function month($month)
    {
        $this->setIntegerParam($this->month, $month);

        return $this;
    }

    /**
     * Filter by Day
     * @param  int $day
     * @return this
     */
    public function day($day)
    {
        $this->setIntegerParam($this->day, $day);

        return $this;
    }

    /**
     * Filter by Status
     * @param  string|array $status one or more statuses
     * @return this
     */
    public function status($status)
    {
        $this->setArrayParam($this->status, $status);

        return $this;
    }

    /**
     * Filter by not Status
     * @param  string|array $notStatus one or more statuses
     * @return this
     */
    public function notStatus($notStatus)
    {
        $this->setArrayParam($this->notStatus, $notStatus);

        return $this;
    }

    /**
     * Order sticky entries first
     * @param  boolean $sticky
     * @return this
     */
    public function sticky($sticky = true)
    {
        $this->setBoolParam($this->sticky, $sticky);

        return $this;
    }
}
