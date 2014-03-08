<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\AbstractJoinableModel;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Collection\TitleCollection;
use rsanchez\Deep\Collection\AbstractTitleCollection;
use DateTime;
use DateTimeZone;

/**
 * Model for the channel_titles table
 */
class Title extends AbstractJoinableModel
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'channel_titles';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'entry_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = array('channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled');

    /**
     * The class used when creating a new Collection
     * @var string
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\TitleCollection';

    /**
     * Global Channel Repository
     * @var \rsanchez\Deep\Repository\ChannelRepository
     */
    protected static $channelRepository;

    /**
     * Define the Member Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('\\rsanchez\\Deep\\Model\\Member', 'author_id', 'member_id');
    }

    /**
     * Define the Member Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('\\rsanchez\\Deep\\Model\\Category', 'category_posts', 'entry_id', 'cat_id');
    }

    /**
     * Set the global ChannelRepository
     * @param  \rsanchez\Deep\Repository\ChannelRepository $channelRepository
     * @return void
     */
    public static function setChannelRepository(ChannelRepository $channelRepository)
    {
        self::$channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return array(
            'members' => function ($query) {
                $query->join('members', 'members.member_id', '=', 'channel_titles.author_id');
            },
        );
    }

    /**
     * {@inheritdoc}
     *
     * Joins with the channel data table, and eager load channels, fields and fieldtypes
     *
     * @param  boolean                               $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted);

        $query->select('channel_titles.*');

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * Hydrate the collection after creation
     *
     * @param  array                                     $models
     * @return \rsanchez\Deep\Collection\TitleCollection
     */
    public function newCollection(array $models = array())
    {
        $collectionClass = $this->collectionClass;

        $collection = new $collectionClass($models);

        if ($models) {
            $this->hydrateCollection($collection);
        }

        return $collection;
    }

    /**
     * Loop through all the hydrators to set Entry custom field attributes
     * @param  \rsanchez\Deep\Collection\TitleCollection $collection
     * @return void
     */
    public function hydrateCollection(AbstractTitleCollection $collection)
    {
        $entryIds = array();
        $channelIds = array();

        foreach ($collection as $entry) {
            $entryIds[] = $entry->entry_id;
            $channelIds[] = $entry->channel_id;

            $entry->channel = self::$channelRepository->find($entry->channel_id);
        }

        $collection->addEntryIds($entryIds);

        $collection->channels = self::$channelRepository->getChannelsById(array_unique($channelIds));
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach (array('entry_date', 'edit_date', 'expiration_date', 'comment_expiration_date', 'recent_comment_date') as $key) {
            if ($attributes[$key] instanceof DateTime) {
                $date = clone $attributes[$key];
                $date->setTimezone(new DateTimeZone('UTC'));
                $attributes[$key] = $date->format('Y-m-d\TH:i:s').'Z';
            }
        }

        return $attributes;
    }

    /**
     * Get the entry_date column as a DateTime object
     *
     * @param  int       $value unix time
     * @return \DateTime
     */
    public function getEntryDateAttribute($value)
    {
        return DateTime::createFromFormat('U', $value);
    }

    /**
     * Get the expiration_date column as a DateTime object, or null if there is no expiration date
     *
     * @param  int            $value unix time
     * @return \DateTime|null
     */
    public function getExpirationDateAttribute($value)
    {
        return $value ? DateTime::createFromFormat('U', $value) : null;
    }

    /**
     * Get the comment_expiration_date column as a DateTime object, or null if there is no expiration date
     *
     * @param  int            $value unix time
     * @return \DateTime|null
     */
    public function getCommentExpirationDateAttribute($value)
    {
        return $value ? DateTime::createFromFormat('U', $value) : null;
    }

    /**
     * Get the recent_comment_date column as a DateTime object, or null if there is no expiration date
     *
     * @param  int            $value unix time
     * @return \DateTime|null
     */
    public function getRecentCommentDateAttribute($value)
    {
        return $value ? DateTime::createFromFormat('U', $value) : null;
    }

    /**
     * Get the edit_date column as a DateTime object
     *
     * @param  int       $value unix time
     * @return \DateTime
     */
    public function getEditDateAttribute($value)
    {
        return DateTime::createFromFormat('YmdHis', $value);
    }

    /**
     * Save the entry (not yet supported)
     *
     * @param  array $options
     * @return void
     */
    public function save(array $options = array())
    {
        throw new \Exception('Saving is not supported');
    }

    /**
     * Filter by Category ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory(Builder $query, $categoryId)
    {
        $categoryIds = array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.cat_id', $categoryIds);
        });
    }

    /**
     * Filter by not Category ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCategory(Builder $query, $categoryId)
    {
        $categoryIds = array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereNotIn('categories.cat_id', $categoryIds);
        });
    }

    /**
     * Filter by Category Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryName(Builder $query, $categoryName)
    {
        $categoryNames = array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryNames) {
            $q->whereIn('categories.cat_name', $categoryNames);
        });
    }

    /**
     * Filter by not Category Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCategoryName(Builder $query, $categoryName)
    {
        $categoryNames = array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryNames) {
            $q->whereNotIn('categories.cat_name', $categoryNames);
        });
    }

    /**
     * Filter by Channel Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $channelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannel(Builder $query, $channelName)
    {
        $channelNames = array_slice(func_get_args(), 1);

        $channels = self::$channelRepository->getChannelsByName($channelNames);

        $channelIds = array();

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        if ($channelIds) {
            array_unshift($channelIds, $query);

            call_user_func_array(array($this, 'scopeChannelId'), $channelIds);
        }

        return $query;
    }

    /**
     * Filter by Channel ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $channelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannelId(Builder $query, $channelId)
    {
        $channelIds = array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.channel_id', $channelIds);
    }

    /**
     * Filter by Author ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $authorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthorId(Builder $query, $authorId)
    {
        $authorIds = array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.author_id', $authorIds);
    }

    /**
     * Filter by not Author ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $authorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotAuthorId(Builder $query, $authorId)
    {
        $authorIds = array_slice(func_get_args(), 1);

        return $query->whereNotIn('channel_titles.author_id', $authorIds);
    }

    /**
     * Filter out Expired Entries
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool                                  $showExpired
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExpired(Builder $query, $showExpired = true)
    {
        if (! $showExpired) {
            $prefix = $query->getQuery()->getConnection()->getTablePrefix();

            $query->whereRaw(
                "(`{$prefix}channel_titles`.`expiration_date` = '' OR  `{$prefix}channel_titles`.`expiration_date` > NOW())"
            );
        }

        return $query;
    }

    /**
     * Filter out Future Entries
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool                                  $showFutureEntries
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowFutureEntries(Builder $query, $showFutureEntries = true)
    {
        if (! $showFutureEntries) {
            $query->where('channel_titles.entry_date', '<=', time());
        }

        return $query;
    }

    /**
     * Set a Fixed Order
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $fixedOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFixedOrder(Builder $query, $fixedOrder)
    {
        $fixedOrder = array_slice(func_get_args(), 1);

        call_user_func_array(array($this, 'scopeEntryId'), func_get_args());

        return $query->orderBy('FIELD('.implode(', ', $fixedOrder).')', 'asc');
    }

    /**
     * Set Sticky Entries to appear first
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool                                  $sticky
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSticky(Builder $query, $sticky = true)
    {
        if ($sticky) {
            array_unshift($query->getQuery()->orders, array('channel_titles.sticky', 'desc'));
        }

        return $query;
    }

    /**
     * Filter by Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryIds = array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.entry_id', $entryIds);
    }

    /**
     * Filter by Not Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $notEntryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotEntryId(Builder $query, $notEntryId)
    {
        $notEntryIds = array_slice(func_get_args(), 1);

        return $query->whereNotIn('channel_titles.entry_id', $notEntryIds);
    }

    /**
     * Filter out entries before the specified Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $entryIdFrom
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryIdFrom(Builder $query, $entryIdFrom)
    {
        return $query->where('channel_titles.entry_id', '>=', $entryIdFrom);
    }

    /**
     * Filter out entries after the specified Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $entryIdTo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryIdTo(Builder $query, $entryIdTo)
    {
        return $query->where('channel_titles.entry_id', '<=', $entryIdTo);
    }

    /**
     * Filter by Member Group ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $groupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupId(Builder $query, $groupId)
    {
        $groupIds = array_slice(func_get_args(), 1);

        return $this->requireTable($query, 'members')->whereIn('members.group_id', $groupIds);
    }

    /**
     * Filter by Not Member Group ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $notGroupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotGroupId(Builder $query, $notGroupId)
    {
        $notGroupIds = array_slice(func_get_args(), 1);

        return $this->requireTable($query, 'members')->whereNotIn('members.group_id', $notGroupIds);
    }

    /**
     * Limit the number of results
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLimit(Builder $query, $limit)
    {
        return $query->take($limit);
    }

    /**
     * Offset the results
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $offset
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOffset(Builder $query, $offset)
    {
        return $query->skip($offset);
    }

    /**
     * Filter out entries before the specified date
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|DateTime                          $startOn unix time
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStartOn(Builder $query, $startOn)
    {
        if ($startOn instanceof DateTime) {
            $startOn = $startOn->format('U');
        }

        return $query->where('channel_titles.entry_date', '>=', $startOn);
    }

    /**
     * Filter by Entry Status
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $status)
    {
        $statuses = array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.status', $statuses);
    }

    /**
     * Filter out entries after the specified date
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|DateTime                          $stopBefore unix time
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStopBefore(Builder $query, $stopBefore)
    {
        if ($stopBefore instanceof DateTime) {
            $stopBefore = $stopBefore->format('U');
        }

        return $query->where('channel_titles.entry_date', '<', $stopBefore);
    }

    /**
     * Filter by URL Title
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $urlTitle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUrlTitle(Builder $query, $urlTitle)
    {
        $urlTitles = array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.url_title', $urlTitles);
    }

    /**
     * Filter by Member Username
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsername(Builder $query, $username)
    {
        $usernames = array_slice(func_get_args(), 1);

        return $this->requireTable($query, 'members')->whereIn('members.username', $usernames);
    }

    /**
     * Filter by Year
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeYear(Builder $query, $year)
    {
        return $query->where('channel_titles.year', $year);
    }

    /**
     * Filter by Month
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth(Builder $query, $month)
    {
        return $query->where('channel_titles.month', $month);
    }

    /**
     * Filter by Day
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $day
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDay(Builder $query, $day)
    {
        return $query->where('channel_titles.day', $day);
    }

    /**
     * Apply an array of parameters
     * @param  array $parameters
     * @return void
     */
    public function scopeTagparams(Builder $query, array $parameters)
    {
        /**
         * A map of parameter names => model scopes
         * @var array
         */
        static $parameterMap = array(
            'author_id' => 'authorId',//explode, not
            'not_author_id' => 'notAuthorId',
            'cat_limit' => 'catLimit',//int
            'category' => 'category',//explode, not
            'not_category' => 'notCategory',
            'category_group' => 'categoryGroup',//explode, not
            'not_category_group' => 'notCategoryGroup',
            'channel' => 'channel',//explode, not
            'display_by' => 'displayBy',//string
            'dynamic_parameters' => 'dynamicParameters',//explode
            'entry_id' => 'entryId',//explode, not
            'not_entry_id' => 'notEntryId',
            'entry_id_from' => 'entryIdFrom',//int
            'entry_id_fo' => 'entryIdTo',//int
            'fixed_order' => 'fixedOrder',//explode
            'group_id' => 'groupId',//explode, not
            'not_group_id' => 'notGroupId',
            'limit' => 'limit',//int
            'month_limit' => 'monthLimit',//int
            'offset' => 'offset',//int
            'orderby' => 'orderby',//string
            //'paginate' => 'paginate',//string
            //'paginate_base' => 'paginateBase',//string
            //'paginate_type' => 'paginateType',//string
            'related_categories_mode' => 'relatedCategoriesMode',//bool
            'relaxed_categories' => 'relaxedCategories',//bool
            'show_current_week' => 'showCurrentWeek',//bool
            'show_expired' => 'showExpired',//bool
            'show_future_entries' => 'showFutureEntries',//bool
            //'show_pages' => 'showPages',
            'sort' => 'sort',
            'start_day' => 'startDay',//string
            'start_on' => 'startOn',//string date
            'status' => 'status',//explode, not
            'not_status' => 'notStatus',
            'sticky' => 'sticky',//bool
            'stop_before' => 'stopBefore',//string date
            'uncategorized_entries' => 'uncategorizedEntries',//bool
            'url_title' => 'urlTitle',//explode, not
            'not_url_title' => 'notUrlTitle',
            'username' => 'username',//explode, not
            'username' => 'notUsername',
            'week_sort' => 'weekSort',//string
            'year' => 'year',
            'month' => 'month',
            'day' => 'day',
        );

        /**
         * A list of parameters that are boolean
         * @var array
         */
        static $boolParameters = array(
            'related_categories_mode',
            'relaxed_categories',
            'show_current_week',
            'show_expired',
            'show_future_entries',
            'sticky',
            'uncategorized_entries',
        );

        /**
         * A list of parameters that are arrays
         * @var array
         */
        static $arrayParameters = array(
            'author_id',
            'category',
            'category_group',
            'channel',
            'dynamic_parameters',
            'entry_id',
            'fixed_order',
            'group_id',
            'status',
            'url_title',
            'username',
        );

        foreach ($parameters as $key => $value) {
            if (! array_key_exists($key, $parameterMap)) {
                continue;
            }

            $method = 'scope'.ucfirst($parameterMap[$key]);

            if (in_array($key, $arrayParameters)) {
                if (array_key_exists('not_'.$key, $parameterMap) && strncmp($value, 'not ', 4) === 0) {
                    $method = 'scope'.ucfirst($parameterMap['not_'.$key]);
                    $args = explode('|', substr($value, 4));
                } else {
                    $args = explode('|', $value);
                }
            } elseif (in_array($key, $boolParameters)) {
                $args = array($value === 'yes');
            } else {
                $args = array($value);
            }

            array_unshift($args, $query);

            call_user_func_array(array($this, $method), $args);
        }

        return $query;
    }
}
