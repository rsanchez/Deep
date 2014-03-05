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
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use DateTime;
use DateTimeZone;

/**
 * Model for the channel_titles table, joined with channel_data
 */
class Entry extends AbstractJoinableModel
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
     * Set a default channel name
     *
     * Useful if extending this class
     * @var string
     */
    protected $channelName;

    /**
     * A map of parameter names => model scopes
     * @var array
     */
    protected static $parameterMap = array(
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
    protected static $boolParameters = array(
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
    protected static $arrayParameters = array(
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

    /**
     * The class used when creating a new Collection
     * @var string
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\EntryCollection';

    /**
     * Global Channel Repository
     * @var \rsanchez\Deep\Repository\ChannelRepository
     */
    public static $channelRepository;

    /**
     * Global Field Repository
     * @var \rsanchez\Deep\Repository\FieldRepository
     */
    public static $fieldRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $nativeClasses = array(
            'rsanchez\Deep\Model\Entry',
            'rsanchez\Deep\Model\PlayaEntry',
            'rsanchez\Deep\Model\RelationshipEntry',
        );

        $class = get_class($this);

        // set the channel name of this class if it's not one of the native classes
        if (! in_array($class, $nativeClasses) && is_null($this->channelName)) {
            $class = basename(str_replace('\\', DIRECTORY_SEPARATOR, $class));
            $this->channelName = snake_case(str_plural($class));
        }
    }

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
     * {@inheritdoc}
     *
     * Load up all the repositories if you haven't already with the Container
     */
    public static function boot()
    {
        parent::boot();

        if (! self::$fieldRepository instanceof FieldRepository) {
            self::setFieldRepository(new FieldRepository(Field::all()));
        }

        if (! self::$channelRepository instanceof ChannelRepository) {
            self::setChannelRepository(new ChannelRepository(Channel::all(), self::$fieldRepository));
        }
    }

    /**
     * Set the global FieldRepository
     * @param \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepository $fieldRepository)
    {
        self::$fieldRepository = $fieldRepository;
    }

    /**
     * Set the global ChannelRepository
     * @param \rsanchez\Deep\Repository\ChannelRepository $channelRepository
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

        $query->addSelect('channel_data.*');

        $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');

        if ($this->channelName) {
            $this->scopeChannel($query, $this->channelName);
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * Hydrate the collection after creation
     *
     * @param  array                                     $models
     * @return \rsanchez\Deep\Collection\EntryCollection
     */
    public function newCollection(array $models = array())
    {
        $collectionClass = $this->collectionClass;

        $collection = new $collectionClass($models);

        $channelIds = array_unique(array_pluck($models, 'channel_id'));

        if ($models) {
            $collection->channels = self::$channelRepository->getChannelsById($channelIds);

            $collection->fields = new FieldCollection();

            foreach ($collection->channels as $channel) {

                $fields = self::$fieldRepository->getFieldsByGroup($channel->field_group);

                foreach ($fields as $field) {
                    $collection->fields->push($field);
                }

            }

            $collection->hydrate();
        }

        return $collection;
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        $attributes = $this->attributes;

        foreach ($attributes as $key => $value) {
            if ($value instanceof DateTime) {
                $date = clone $value;
                $date->setTimezone(new DateTimeZone('UTC'));
                $attributes[$key] = $date->format('Y-m-d\TH:i:s').'Z';
            }
        }

        return $this->getArrayableItems($attributes);
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
     * {@inheritdoc}
     */
    public function toArray()
    {
        $hidden =& $this->hidden;

        // remove field_id_X fields from the array
        foreach (array_keys($this->attributes) as $key) {
            if (preg_match('#^field_(id|dt|ft)_#', $key)) {
                $this->hidden[] = $key;
            }
        }

        $array = parent::toArray();

        $this->channel->fields->each(function ($field) use (&$array) {
            if (isset($array[$field->field_name]) && method_exists($array[$field->field_name], 'toArray')) {
                $array[$field->field_name] = $array[$field->field_name]->toArray();
            }
        });

        return $array;
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
     * Get the Channel model associated with this entry
     *
     * @param  mixed                             $value
     * @return \rsanchez\Deep\Model\Channel|null
     */
    public function getChannelAttribute($value)
    {
        if (self::$channelRepository instanceof ChannelRepository) {
            return self::$channelRepository->getChannelById($this->channel_id);
        }

        return $value;
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
     * @param  string|array                          $category_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory(Builder $query, $category_id)
    {
        $category_id = is_array($category_id) ? $category_id : array($category_id);

        return $query->whereHas('categories', function ($q) use ($category_id) {
            $q->whereIn('categories.cat_id', $category_id);
        });
    }

    /**
     * Filter by Category Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $category_name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryName(Builder $query, $category_name)
    {
        $category_name = is_array($category_name) ? $category_name : array($category_name);

        return $query->whereHas('categories', function ($q) use ($category_name) {
            $q->whereIn('categories.cat_name', $category_name);
        });
    }

    /**
     * Filter by Channel Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $channelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannel(Builder $query, $channelName)
    {
        $channelName = is_array($channelName) ? $channelName : array($channelName);

        $channels = self::$channelRepository->getChannelsByName($channelName);

        $channelIds = array();

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        return $channelIds ? $this->scopeChannelId($query, $channelIds) : $query;
    }

    /**
     * Filter by Channel ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $channelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannelId(Builder $query, $channelId)
    {
        $channelId = is_array($channelId) ? $channelId : array($channelId);

        return $query->whereIn('channel_titles.channel_id', $channelId);
    }

    /**
     * Filter by Author ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $authorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthorId(Builder $query, $authorId)
    {
        $authorId = is_array($authorId) ? $authorId : array($authorId);

        return $query->whereIn('channel_titles.author_id', $authorId);
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
     * @param  array                                 $fixedOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFixedOrder(Builder $query, array $fixedOrder)
    {
        return $this->scopeEntryId($query, $fixedOrder)
                    ->orderBy('FIELD('.implode(', ', $fixedOrder).')', 'asc');
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
     * @param  string|array                          $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $query->whereIn('channel_titles.entry_id', $entryId);
    }

    /**
     * Filter by Not Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $notEntryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotEntryId(Builder $query, $notEntryId)
    {
        $notEntryId = is_array($notEntryId) ? $notEntryId : array($notEntryId);

        return $query->whereNotIn('channel_titles.entry_id', $notEntryId);
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
     * @param  int|array                             $groupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupId(Builder $query, $groupId)
    {
        $groupId = is_array($groupId) ? $groupId : array($groupId);

        return $this->requireTable($query, 'members')->whereIn('members.group_id', $groupId);
    }

    /**
     * Filter by Not Member Group ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $notGroupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotGroupId(Builder $query, $notGroupId)
    {
        $notGroupId = is_array($notGroupId) ? $notGroupId : array($notGroupId);

        return $this->requireTable($query, 'members')->whereNotIn('members.group_id', $notGroupId);
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
     * @param  string|array                          $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $status)
    {
        $status = is_array($status) ? $status : array($status);

        return $query->whereIn('channel_titles.status', $status);
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
     * @param  string|array                          $urlTitle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUrlTitle(Builder $query, $urlTitle)
    {
        $urlTitle = is_array($urlTitle) ? $urlTitle : array($urlTitle);

        return $query->whereIn('channel_titles.url_title', $urlTitle);
    }

    /**
     * Filter by Member Username
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsername(Builder $query, $username)
    {
        $username = is_array($username) ? $username : array($username);

        return $this->requireTable($query, 'members')->whereIn('members.username', $username);
    }

    /**
     * Filter by Custom Field Search
     */
    public function scopeSearch(Builder $query, array $search)
    {
        $this->requireTable($query, 'channel_data');

        foreach ($search as $fieldName => $values) {
            if (self::$fieldRepository->hasField($fieldName)) {

                $fieldId = self::$fieldRepository->getFieldId($fieldName);

                $query->where(function ($query) use ($fieldId, $values) {

                    foreach ($values as $value) {
                        $query->orWhere('channel_data.field_id_'.$fieldId, 'LIKE', '%{$value}%');
                    }

                });
            }
        }

        return $query;
    }

    /**
     * Apply an array of parameters
     * @param  array $parameters
     * @return void
     */
    public function scopeTagparams(Builder $query, array $parameters)
    {
        $search = array();

        foreach ($parameters as $key => $value) {
            if (strncmp($key, 'search:', 7) === 0) {
                $key = 'search';
                $search[substr($key, 7)] = explode('|', $value);
                continue;
            }

            if (! array_key_exists($key, static::$parameterMap)) {
                continue;
            }

            $method = 'scope'.ucfirst(static::$parameterMap[$key]);

            if (in_array($key, static::$arrayParameters)) {
                if (array_key_exists('not_'.$key, static::$parameterMap) && strncmp($value, 'not ', 4) === 0) {
                    $method = 'scope'.ucfirst(static::$parameterMap['not_'.$key]);
                    $value = explode('|', substr($value, 4));
                } else {
                    $value = explode('|', $value);
                }
            } elseif (in_array($key, static::$boolParameters)) {
                $value = $value === 'yes';
            }

            $this->$method($query, $value);
        }

        if ($search) {
            $this->scopeSearch($query, $search);
        }

        return $query;
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
}
