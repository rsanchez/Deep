<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use \Illuminate\Database\Query\Builder as QueryBuilder;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Builder;
use rsanchez\Deep\Collection\EntryCollection;
use DateTime;
use DateTimeZone;

/**
 * Model for the channel_titles table, joined with channel_data
 */
class Entry extends Model
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
    protected $hidden = array('channel');

    /**
     * Custom fields, keyed by name
     *   field_name => \rsanchez\Deep\Model\Field
     * @var array
     */
    protected $fieldsByName = array();

    /**
     * Join tables
     * @var array
     */
    protected static $tables = array(
        'members' => array('members.member_id', 'channel_titles.author_id'),
        'channels' => array('channels.channel_id', 'channel_titles.channel_id'),
    );

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
        'channel' => 'channelName',//explode, not
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
     * A link to the Builder's relation cache
     * @var array
     */
    protected $builderRelationCache;

    /**
     * Define the Channel Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo('\\rsanchez\\Deep\\Model\\Channel');
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

        $query->with('channel', 'channel.fields', 'channel.fields.fieldtype');

        $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');

        return $query;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new Builder($query);

        $this->builderRelationCache =& $builder->relationCache;

        return $builder;
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
        $collection = new EntryCollection($models);

        $collection->channels = $this->builderRelationCache['channel'];

        if ($models) {
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

        foreach ($attributes as $key => $value)
        {
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
        $fieldColumns = array_filter(array_keys($this->attributes), function ($key) {
            return preg_match('#^field_(id|dt|ft)_#', $key) === 1;
        });

        array_walk($fieldColumns, function ($key) use (&$hidden) {
            $hidden[] = $key;
        });

        return parent::toArray();
    }

    /**
     * {@inheritdoc}
     *
     * Get custom field value, if key is a custom field name
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $hasAttribute = array_key_exists($key, $this->attributes);
        $hasChannel = isset($this->channel) && isset($this->channel->fields);

        if (! $hasAttribute && $hasChannel && $this->channel->fields->hasField($key)) {
            $this->attributes[$key] = $this->attributes['field_id_'.$this->channel->fields->getFieldId($key)];
        }

        return parent::getAttribute($key);
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
     * Apply an array of parameters
     * @param  array $parameters
     * @return void
     */
    public function applyParameters(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            /*
            if (strncmp($key, 'search:', 7) === 0) {
                $key = 'search';
            }
            */

            if (! array_key_exists($key, static::$methodMap)) {
                continue;
            }

            $method = static::$methodMap[$key];

            if (in_array($key, static::$arrayParameters)) {
                if (array_key_exists('not_'.$key, static::$methodMap) && strncmp($value, 'not ', 4) === 0) {
                    $method = static::$methodMap['not_'.$key];
                    $value = explode('|', substr($value, 4));
                } else {
                    $value = explode('|', $value);
                }
            } elseif (in_array($key, static::$boolParameters)) {
                $value = $value === 'yes';
            }

            $this->$method($value);
        }
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
     * Filter by Channel Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|array                          $channelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannelName(Builder $query, $channelName)
    {
        $channelName = is_array($channelName) ? $channelName : array($channelName);

        return $this->requireTable($query, 'channels')->whereIn('channels.channel_name', $channelName);
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
     * @TODO how to get custom field names
     */
    public function scopeSearch(Builder $query, array $search)
    {
        $this->requireTable($query, 'channel_data');

        foreach ($search as $fieldName => $values) {
            try {
                $field = $this->channelFieldRepository->find($fieldName);

                $query->where(function ($query) use ($field, $values) {

                    foreach ($values as $value) {
                        $query->orWhere('channel_data.field_id_'.$field->id(), 'LIKE', '%{$value}%');
                    }

                });

            } catch (Exception $e) {
                //$e->getMessage();
            }
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

    /**
     * Register all custom fields with this entry
     * @return void
     */
    protected function registerFields()
    {
        static $fieldsRegistered = false;

        if ($fieldsRegistered === false && isset($this->channel) && isset($this->channel->fields)) {
            $fieldsRegistered = true;

            $fieldsByName =& $this->fieldsByName;

            $this->channel->fields->each(function ($field) use (&$fieldsByName) {
                $fieldsByName[$field->field_name] = $field;
            });
        }
    }

    /**
     * Join the required table, once
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $which table name
     * @param  bool                                  $select whether to select this table's columns
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    protected function requireTable(Builder $query, $which, $select = false)
    {
        if (! isset(static::$tables[$which])) {
            return $query;
        }

        foreach ($query->getQuery()->joins as $joinClause) {
            if ($joinClause->table === $which) {
                return $query;
            }
        }

        if ($select) {
            $query->addSelect($which.'.*');
        }

        return $query->join($which, static::$tables[$which][0], '=', static::$tables[$which][1]);
    }
}
