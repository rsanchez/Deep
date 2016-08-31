<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\CategoryCollection;
use rsanchez\Deep\Hydrator\HydratorFactory;
use rsanchez\Deep\Relations\HasOneFromRepository;
use rsanchez\Deep\Validation\ValidatableInterface;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use Carbon\Carbon;
use Closure;
use DateTime;

/**
 * Model for the channel_titles table, joined with channel_data
 */
class Entry extends AbstractEntity
{
    use JoinableTrait, GlobalAttributeVisibilityTrait, HasChannelRepositoryTrait, HasSiteRepositoryTrait, HasFieldRepositoryTrait;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected static $globalHidden = [
        'site_id',
        'forum_topic_id',
        'ip_address',
        'versioning_enabled',
        'comments',
    ];

    /**
     * @var \rsanchez\Deep\Model\ChannelData
     */
    protected $channelData;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected static $globalVisible = [];

    /**
     * List of extra hydrators to load (e.g. parents or siblings)
     * @var array
     */
    protected $extraHydrators = [];

    /**
     * When extending this class, set this property to automatically
     * load from the specified channel
     * @var string|null
     */
    protected $defaultChannelName;

    /**
     * {@inheritdoc}
     */
    protected $customFieldAttributesRegex = '/^field_(id|dt|ft)_\d+$/';

    /**
     * {@inheritdoc}
     */
    protected $hiddenAttributesRegex = '/^field_(id|dt|ft)_\d+$/';

    /**
     * {@inheritdoc}
     */
    const CREATED_AT = 'entry_date';

    /**
     * {@inheritdoc}
     */
    const UPDATED_AT = 'edit_date';

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'view_count_one' => 0,
        'view_count_two' => 0,
        'view_count_three' => 0,
        'view_count_four' => 0,
        'site_id' => 1,
        'versioning_enabled' => 'n',
        'allow_comments' => 'y',
        'sticky' => 'n',
        'comment_total' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'channel_id' => 'required|exists:channels,channel_id',
        'author_id' => 'required|exists:members,member_id',
        'forum_topic_id' => 'exists:forum_topics,forum_topic_id',
        'ip_address' => 'ip',
        'title' => 'required',
        'url_title' => 'required|alpha_dash|unique:channel_titles,url_title',
        'status' => 'required',
        'versioning_enabled' => 'required|yes_or_no',
        'view_count_one' => 'required|integer',
        'view_count_two' => 'required|integer',
        'view_count_three' => 'required|integer',
        'view_count_four' => 'required|integer',
        'allow_comments' => 'required|yes_or_no',
        'sticky' => 'required|yes_or_no',
        'entry_date' => 'date_format:U',
        'year' => 'integer',
        'month' => 'digits:2',
        'day' => 'digits:2',
        'expiration_date' => 'date_format:U',
        'comment_expiration_date' => 'date_format:U',
        'edit_date' => 'date_format:YmdHis',
        'recent_comment_date' => 'date_format:U',
        'comment_total' => 'required|integer',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributeNames = [
        'site_id' => 'Site ID',
        'channel_id' => 'Channel ID',
        'author_id' => 'Author ID',
        'forum_topic_id' => 'Forum Topic ID',
        'ip_address' => 'IP Address',
        'title' => 'Title',
        'url_title' => 'URL Title',
        'status' => 'Status',
        'versioning_enabled' => 'Versioning Enabled',
        'view_count_one' => 'View Count One',
        'view_count_two' => 'View Count Two',
        'view_count_three' => 'View Count Three',
        'view_count_four' => 'View Count Four',
        'allow_comments' => 'Allow Comments',
        'sticky' => 'Sticky',
        'entry_date' => 'Entry Date',
        'year' => 'Year',
        'month' => 'Month',
        'day' => 'Day',
        'expiration_date' => 'Expiration Date',
        'comment_expiration_date' => 'Comment Expiration Date',
        'edit_date' => 'Edit Date',
        'recent_comment_date' => 'Recent Comment Date',
        'comment_total' => 'Comment Total',
    ];

    /**
     * Whether or not to hydrate custom fields
     * @var bool
     */
    protected static $hydrationEnabled = true;

    /**
     * Whether or not to hydrate children's custom fields
     * @var bool
     */
    protected static $childHydrationEnabled = true;

    /**
     * The class used when creating a new Collection
     * @var string
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\EntryCollection';

    /**
     * Define the Author Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Member', 'member_id', 'author_id');
    }

    /**
     * Define the Categories Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categories()
    {
        return $this->hasManyThrough('\\rsanchez\\Deep\\Model\\Category', '\\rsanchez\\Deep\\Model\\CategoryPosts', 'entry_id', 'cat_id');
    }

    /**
     * Define the Channel Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasOneFromRepository
     */
    public function chan()
    {
        return new HasOneFromRepository(
            static::getChannelRepository()->getModel()->newQuery(),
            $this,
            'channels.channel_id',
            'channel_id',
            static::getChannelRepository()
        );
    }

    /**
     * Define the Comments Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\Comment', 'entry_id', 'entry_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->entry_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'entry';
    }

    /**
     * Set the channel_id attribute for this entry
     * @param $channelId
     */
    public function setChannelIdAttribute($channelId)
    {
        $this->setChannel(static::getChannelRepository()->find($channelId));
    }

    /**
     * {@inheritdoc}
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->channelData = $this->newChannelData();

        $channelDataAttributes = [];

        if ($this->customFieldAttributesRegex) {
            foreach ($attributes as $key => $value) {
                if (preg_match($this->customFieldAttributesRegex, $key)) {
                    $channelDataAttributes[$key] = $value;

                    unset($attributes[$key]);
                } elseif ($key === 'entry_id' || $key === 'site_id' || $key === 'channel_id') {
                    $channelDataAttributes[$key] = $value;
                }
            }
        }

        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        $this->channelData->setRawAttributes($channelDataAttributes, $sync);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * Set the Channel model for this entry
     * @param Channel $channel
     */
    public function setChannel(Channel $channel)
    {
        $this->setRelation('chan', $channel);

        $this->attributes['channel_id'] = $channel->channel_id;

        $this->setDehydrators($this->getHydratorFactory()->getDehydrators($channel->fields));

        $this->hydrateDefaultProperties();
    }

    /**
     * Alias to chan relationship
     * @return \rsanchez\Deep\Model\Channel
     */
    public function getChannelAttribute()
    {
        return $this->chan;
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return [
            'members' => function ($query) {
                $query->join('members', 'members.member_id', '=', 'channel_titles.author_id');
            },
            'category_posts' => function ($query) {
                $query->join('category_posts', 'category_posts.entry_id', '=', 'channel_titles.entry_id');
            },
        ];
    }

    /**
     * Loop through all the hydrators to set Entry custom field attributes
     * @param  \rsanchez\Deep\Collection\EntryCollection $collection
     * @return void
     */
    public function hydrateCollection(EntryCollection $collection)
    {
        $hydrationEnabled = self::$hydrationEnabled;

        self::$hydrationEnabled = self::$childHydrationEnabled;

        $hydrators = static::getHydratorFactory()->getHydratorsForCollection($collection, $this->extraHydrators);
        $dehydrators = static::getHydratorFactory()->getDehydratorsForCollection($collection);

        // loop through the hydrators for preloading
        foreach ($hydrators as $hydrator) {
            $hydrator->preload($collection);
        }

        // loop again to actually hydrate
        foreach ($collection as $entry) {
            $entry->setHydrators($hydrators);

            $entry->setDehydrators($dehydrators);

            foreach ($entry->channel->fields as $field) {
                $hydrator = $hydrators->get($field->getType());

                if ($hydrator) {
                    $value = $hydrator->hydrate($entry, $field);
                } else {
                    $value = $entry->{$field->getIdentifier()};
                }

                $entry->setCustomField($field->getName(), $value);
            }

            foreach ($this->extraHydrators as $name) {
                $entry->setCustomField($name, $hydrators[$name]->hydrate($entry, new NullProperty()));
            }
        }

        self::$hydrationEnabled = $hydrationEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        $dateAttributes = ['edit_date', 'expiration_date', 'comment_expiration_date', 'recent_comment_date'];

        foreach ($dateAttributes as $key) {
            if (isset($attributes[$key]) && $attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $attributes;
    }

    /**
     * Set the entry_date column
     * @param  DateTime|string|int $date
     * @return void
     */
    public function setEntryDateAttribute($date)
    {
        $this->attributes['entry_date'] = $date instanceof DateTime ? $date->format('U') : $date;

        if (! $date instanceof DateTime) {
            $date = Carbon::createFromFormat('U', $date);
        }

        $this->attributes['year'] = $date->format('Y');
        $this->attributes['month'] = $date->format('m');
        $this->attributes['day'] = $date->format('d');
    }

    /**
     * Get the expiration_date column as a Carbon object, or null if there is no expiration date
     *
     * @param  int                 $value unix time
     * @return \Carbon\Carbon|null
     */
    public function getExpirationDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('U', $value) : null;
    }

    /**
     * Set the expiration_date column
     * @param  DateTime|string|int|null $date
     * @return void
     */
    public function setExpirationDateAttribute($date)
    {
        $this->attributes['expiration_date'] = $date instanceof DateTime ? $date->format('U') : $date;
    }

    /**
     * Get the comment_expiration_date column as a Carbon object, or null if there is no expiration date
     *
     * @param  int                 $value unix time
     * @return \Carbon\Carbon|null
     */
    public function getCommentExpirationDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('U', $value) : null;
    }

    /**
     * Set the comment_expiration_date column
     * @param  DateTime|string|int|null $date
     * @return void
     */
    public function setCommentExpirationDateAttribute($date)
    {
        $this->attributes['comment_expiration_date'] = $date instanceof DateTime ? $date->format('U') : $date;
    }

    /**
     * Get the recent_comment_date column as a Carbon object, or null if there is no expiration date
     *
     * @param  int                 $value unix time
     * @return \Carbon\Carbon|null
     */
    public function getRecentCommentDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('U', $value) : null;
    }

    /**
     * Set the recent_comment_date column
     * @param  DateTime|string|int|null $date
     * @return void
     */
    public function setRecentCommentDateAttribute($date)
    {
        $this->attributes['recent_comment_date'] = $date instanceof DateTime ? $date->format('U') : $date;
    }

    /**
     * Get the edit_date column as a Carbon object
     *
     * @param  int            $value unix time
     * @return \Carbon\Carbon
     */
    public function getEditDateAttribute($value)
    {
        return Carbon::createFromFormat('YmdHis', $this->attributes['edit_date']);
    }

    /**
     * {@inheritdoc}
     */
    public function freshTimestampString()
    {
        return $this->freshTimestamp()->format('YmdHis');
    }

    /**
     * Set the edit_date column
     * @param  DateTime|string|int $date
     * @return void
     */
    public function setEditDateAttribute($date)
    {
        $this->attributes['edit_date'] = $date instanceof DateTime ? $date->format('YmdHis') : $date;
    }

    /**
     * Get the page_uri of the entry
     *
     * @return string|null
     */
    public function getPageUriAttribute()
    {
        return static::getSiteRepository()->getPageUri($this->entry_id);
    }

    /**
     * Get the channel_name of the entry's channel
     *
     * @return string
     */
    public function getChannelNameAttribute()
    {
        return $this->channel_id ? $this->channel->channel_name : '';
    }

    /**
     * Get the channel_name of the entry's channel
     *
     * @return string
     */
    public function getChannelShortNameAttribute()
    {
        return $this->channel_id ? $this->channel->channel_name : '';
    }

    /**
     * Get the username of the entry's author
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        return $this->author_id ? $this->author->username : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = $this->getDefaultValidationRules($validatorFactory, $property);

        $rules['entry_date'] = 'required|'.$rules['entry_date'];
        $rules['year'] = 'required|'.$rules['year'];
        $rules['month'] = 'required|'.$rules['month'];
        $rules['day'] = 'required|'.$rules['day'];

        $rules['url_title'] .= sprintf(',%s,entry_id', $this->entry_id);

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = $this->rules;

        if ($this->channel_id && $this->channel->status_group) {
            $rules['status'] .= sprintf('|exists:statuses,status,group_id,%s', $this->channel->status_group);
        } else {
            $rules['status'] .= '|in:open,closed';
        }

        return $rules;
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

        if ($this->defaultChannelName) {
            $this->scopeChannel($query, $this->defaultChannelName);
        }

        if (self::$hydrationEnabled) {
            $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function newCollection(array $models = [])
    {
        $method = "{$this->collectionClass}::create";

        $fieldRepository = self::$hydrationEnabled ? static::getFieldRepository() : null;

        $collection = call_user_func($method, $models, static::getChannelRepository(), $fieldRepository);

        if ($models && self::$hydrationEnabled) {
            $this->hydrateCollection($collection);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        if (!$this->channel_id || !self::$hydrationEnabled) {
            return new FieldCollection();
        }

        return $this->channel->fields;
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
            if ($attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $this->getArrayableItems($attributes);
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
        $categoryIds = is_array($categoryId) ? $categoryId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.cat_id', $categoryIds);
        });
    }

    /**
     * Get entries that are share one or more categories with the specified entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelatedCategories(Builder $query, $entryId)
    {
        $connection = $query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        return $this->requireTable($query, 'category_posts')
            ->join($connection->raw("`{$tablePrefix}category_posts` AS `{$tablePrefix}category_posts_2`"), 'category_posts_2.cat_id', '=', 'category_posts.cat_id')
            ->where('category_posts_2.entry_id', $entryId)
            ->where('channel_titles.entry_id', '!=', $entryId)
            ->groupBy('channel_titles.entry_id');
    }

    /**
     * Get entries that are share one or more categories with the specified entry url title
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $urlTitle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelatedCategoriesUrlTitle(Builder $query, $urlTitle)
    {
        $connection = $query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        return $this->requireTable($query, 'category_posts')
            ->join($connection->raw("`{$tablePrefix}category_posts` AS `{$tablePrefix}category_posts_2`"), 'category_posts_2.cat_id', '=', 'category_posts.cat_id')
            ->join($connection->raw("`{$tablePrefix}channel_titles` AS `{$tablePrefix}channel_titles_2`"), 'channel_titles_2.entry_id', '=', 'category_posts_2.entry_id')
            ->where('channel_titles_2.url_title', $urlTitle)
            ->where('channel_titles.url_title', '!=', $urlTitle)
            ->groupBy('channel_titles.entry_id');
    }

    /**
     * Filter out entries without all Category IDs
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllCategories(Builder $query, $categoryId)
    {
        $categoryIds = is_array($categoryId) ? $categoryId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {

            $q->where(function ($qq) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $qq->orWhere('categories.cat_id', $categoryId);
                }
            });

        }, '>=', count($categoryIds));
    }

    /**
     * Filter out entries without all Category IDs
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotAllCategories(Builder $query, $categoryId)
    {
        $categoryIds = is_array($categoryId) ? $categoryId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {

            $q->where(function ($qq) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $qq->orWhere('categories.cat_id', $categoryId);
                }
            });

        }, '<', count($categoryIds));
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
        $categoryIds = is_array($categoryId) ? $categoryId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.cat_id', $categoryIds);
        }, '=', 0);
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
        $categoryNames = is_array($categoryName) ? $categoryName : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryNames) {
            $q->whereIn('categories.cat_url_title', $categoryNames);
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
        $categoryNames = is_array($categoryName) ? $categoryName : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($categoryNames) {
            $q->whereIn('categories.cat_url_title', $categoryNames);
        }, '=', 0);
    }

    /**
     * Filter by Category Group
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $groupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryGroup(Builder $query, $groupId)
    {
        $groupIds = is_array($groupId) ? $groupId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($groupIds) {
            $q->whereIn('categories.group_id', $groupIds);
        });
    }

    /**
     * Filter by Not Category Group
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $groupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCategoryGroup(Builder $query, $groupId)
    {
        $groupIds = is_array($groupId) ? $groupId : array_slice(func_get_args(), 1);

        return $query->whereHas('categories', function ($q) use ($groupIds) {
            $q->whereIn('categories.group_id', $groupIds);
        }, '=', 0);
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
        $channelNames = is_array($channelName) ? $channelName : array_slice(func_get_args(), 1);

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $channelIds = [];

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        if ($channelIds) {
            array_unshift($channelIds, $query);

            call_user_func_array([$this, 'scopeChannelId'], $channelIds);
        }

        return $query;
    }

    /**
     * Filter by not Channel Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $channelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotChannel(Builder $query, $channelName)
    {
        $channelNames = is_array($channelName) ? $channelName : array_slice(func_get_args(), 1);

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $channelIds = [];

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        if ($channelIds) {
            array_unshift($channelIds, $query);

            call_user_func_array([$this, 'scopeNotChannelId'], $channelIds);
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
        $channelIds = is_array($channelId) ? $channelId : array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.channel_id', $channelIds);
    }

    /**
     * Filter by not Channel ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  int                          $channelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotChannelId(Builder $query, $channelId)
    {
        $channelIds = is_array($channelId) ? $channelId : array_slice(func_get_args(), 1);

        return $query->whereNotIn('channel_titles.channel_id', $channelIds);
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
        $authorIds = is_array($authorId) ? $authorId : array_slice(func_get_args(), 1);

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
        $authorIds = is_array($authorId) ? $authorId : array_slice(func_get_args(), 1);

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
            $query->where(function ($query) {
                return $query->where('expiration_date', '')
                    ->orWhere('expiration_date', '>', time());
            });
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
     * Filter by site ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $siteId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSiteId(Builder $query, $siteId)
    {
        $siteIds = is_array($siteId) ? $siteId : array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.site_id', $siteIds);
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
        $fixedOrder = is_array($fixedOrder) ? $fixedOrder : array_slice(func_get_args(), 1);

        call_user_func_array([$this, 'scopeEntryId'], func_get_args());

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
            $orders = & $query->getQuery()->orders;

            $order = [
                'column' => 'channel_titles.sticky',
                'direction' => 'desc',
            ];

            if ($orders) {
                array_unshift($orders, $order);
            } else {
                $orders = [$order];
            }
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
        $entryIds = is_array($entryId) ? $entryId : array_slice(func_get_args(), 1);

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
        $notEntryIds = is_array($notEntryId) ? $notEntryId : array_slice(func_get_args(), 1);

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
        $groupIds = is_array($groupId) ? $groupId : array_slice(func_get_args(), 1);

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
        $notGroupIds = is_array($notGroupId) ? $notGroupId : array_slice(func_get_args(), 1);

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
     * Filter by Page
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool|string                           $showPages
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowPages(Builder $query, $showPages = true)
    {
        if (! $showPages) {
            $args = static::getSiteRepository()->getPageEntryIds();

            array_unshift($args, $query);

            call_user_func_array([$this, 'scopeNotEntryId'], $args);
        }

        return $query;
    }

    /**
     * Filter by Pages Only
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool|string                           $showPagesOnly
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowPagesOnly(Builder $query, $showPagesOnly = true)
    {
        if ($showPagesOnly) {
            $args = static::getSiteRepository()->getPageEntryIds();

            array_unshift($args, $query);

            call_user_func_array([$this, 'scopeEntryId'], $args);
        }

        return $query;
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
        $statuses = is_array($status) ? $status : array_slice(func_get_args(), 1);

        return $query->whereIn('channel_titles.status', $statuses);
    }

    /**
     * Filter by Entry Status
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotStatus(Builder $query, $status)
    {
        $statuses = is_array($status) ? $status : array_slice(func_get_args(), 1);

        return $query->whereNotIn('channel_titles.status', $statuses);
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
        $urlTitles = is_array($urlTitle) ? $urlTitle : array_slice(func_get_args(), 1);

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
        $usernames = is_array($username) ? $username : array_slice(func_get_args(), 1);

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
     * @param  string|int                            $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth(Builder $query, $month)
    {
        return $query->where('channel_titles.month', str_pad($month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * Filter by Day
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|int                            $day
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDay(Builder $query, $day)
    {
        return $query->where('channel_titles.day', str_pad($day, 2, '0', STR_PAD_LEFT));
    }

    /**
     * Call the specified scope, exploding a pipe-delimited string into an array
     * Calls the not version of the scope if the string begins with not
     * eg  'not 4|5|6'
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $string ex '4|5|6' 'not 4|5|6'
     * @param string                                $scope  the name of the scope, ex. AuthorId
     */
    protected function scopeArrayFromString(Builder $query, $string, $scope)
    {
        if ($not = strncmp($string, 'not ', 4) === 0) {
            $string = substr($string, 4);
        }

        $args = explode('|', $string);

        $method = 'scope'.$scope;

        if ($not && method_exists($this, 'scopeNot'.$scope)) {
            $method = 'scopeNot'.$scope;
        }

        array_unshift($args, $query);

        return call_user_func_array([$this, $method], $args);
    }

    /**
     * Filter by Author ID string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuthorIdString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'AuthorId');
    }

    /**
     * Filter by Category string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryString(Builder $query, $string)
    {
        if ($not = strncmp($string, 'not ', 4) === 0) {
            $string = substr($string, 4);
        }

        $type = strpos($string, '&') !== false ? '&' : '|';

        $args = explode($type, $string);

        if ($type === '&') {
            $method = $not ? 'scopeNotAllCategories' : 'scopeAllCategories';
        } else {
            $method = $not ? 'scopeNotCategory' : 'scopeCategory';
        }

        array_unshift($args, $query);

        return call_user_func_array([$this, $method], $args);
    }

    /**
     * Filter by Category Group string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryGroupString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'CategoryGroup');
    }

    /**
     * Filter by Category Name string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryNameString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'CategoryName');
    }

    /**
     * Filter by Channel string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChannelString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'Channel');
    }

    /**
     * Filter by Entry ID string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryIdString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'EntryId');
    }

    /**
     * Filter by Fixed Order string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFixedOrderString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'FixedOrder');
    }

    /**
     * Filter by Member Group ID string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupIdString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'GroupId');
    }

    /**
     * Filter by Show Expired string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExpiredString(Builder $query, $string)
    {
        return $this->scopeShowExpired($query, $string === 'yes');
    }

    /**
     * Filter by Show Future Entries string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowFutureEntriesString(Builder $query, $string)
    {
        return $this->scopeShowFutureEntries($query, $string === 'yes');
    }

    /**
     * Filter by Show Pages string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowPagesString(Builder $query, $string)
    {
        if ($string === 'only') {
            return $this->scopeShowPagesOnly($query);
        }

        return $this->scopeShowPages($query, $string === 'yes');
    }

    /**
     * Filter by Status string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'Status');
    }

    /**
     * Filter by Sticky string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStickyString(Builder $query, $string)
    {
        return $this->scopeSticky($query, $string === 'yes');
    }

    /**
     * Filter by URL Title string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUrlTitleString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'UrlTitle');
    }

    /**
     * Filter by Username string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsernameString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'Username');
    }

    /**
     * Eager load categories
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Closure|null                          $callback eager load constraint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCategories(Builder $query, Closure $callback = null)
    {
        $with = $callback ? ['categories' => $callback] : 'categories';

        return $query->with($with);
    }

    /**
     * Eager load categories with custom fields
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Closure|null                          $callback eager load constraint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCategoryFields(Builder $query, Closure $callback = null)
    {
        return $this->scopeWithCategories($query, function ($query) use ($callback) {
            if ($callback) {
                $callback($query);
            }

            return $query->withFields();
        });
    }

    /**
     * Eager load author
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Closure|null                          $callback eager load constraint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAuthor(Builder $query, Closure $callback = null)
    {
        $with = $callback ? ['author' => $callback] : 'author';

        return $query->with($with);
    }

    /**
     * Eager load author with custom fields
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Closure|null                          $callback eager load constraint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAuthorFields(Builder $query, Closure $callback = null)
    {
        return $this->scopeWithAuthor($query, function ($query) use ($callback) {
            if ($callback) {
                $callback($query);
            }

            return $query->withFields();
        });
    }

    /**
     * Eager load author
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  Closure|null                          $callback eager load constraint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithComments(Builder $query, Closure $callback = null)
    {
        return $query->with(['comments' => function ($query) use ($callback) {
            if ($callback) {
                $callback($query);
            }

            return $query->with('author');
        }]);
    }

    /**
     * Dynamically apply scopes
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $allowedParameters list of keys to pull from $request
     * @param  array                                 $request           array of request variables, for instance $_REQUEST
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDynamicParameters(Builder $query, array $allowedParameters, array $request)
    {
        foreach ($allowedParameters as $key) {
            if (isset($request[$key])) {
                $this->scopeTagparam($query, $key, $request[$key]);
            }
        }

        return $query;
    }

    /**
     * Hydrate the parents property
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithParents(Builder $query)
    {
        $this->extraHydrators[] = 'parents';

        return $query;
    }

    /**
     * Hydrate the siblings property
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSiblings(Builder $query)
    {
        $this->extraHydrators[] = 'siblings';

        return $query;
    }

    /**
     * Scope to turn of custom field hydration
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutFields(Builder $query)
    {
        self::$hydrationEnabled = false;

        // remove the channel_data join
        foreach ($query->getQuery()->joins as $i => $join) {
            if ($join->table === 'channel_data') {
                unset($query->getQuery()->joins[$i]);
                break;
            }
        }

        return $query;
    }

    /**
     * Scope to turn of child entry custom field hydration
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutChildFields(Builder $query)
    {
        self::$childHydrationEnabled = false;

        return $query;
    }

    /**
     * Apply a single parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $key   snake_cased parameter name
     * @param  string                                $value scope parameters in string form, eg. 1|2|3
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTagparam(Builder $query, $key, $value)
    {
        /**
         * A map of parameter names => model scopes
         * @var array
         */
        static $parameterMap = [
            'author_id' => 'authorIdString',
            'cat_limit' => 'catLimit',
            'category' => 'categoryString',
            'category_name' => 'categoryNameString',
            'category_group' => 'categoryGroupString',
            'channel' => 'channelString',
            'entry_id' => 'entryIdString',
            'entry_id_from' => 'entryIdFrom',
            'entry_id_fo' => 'entryIdTo',
            'fixed_order' => 'fixedOrderString',
            'group_id' => 'groupIdString',
            'limit' => 'limit',
            'offset' => 'offset',
            'show_expired' => 'showExpiredString',
            'show_future_entries' => 'showFutureEntriesString',
            'show_pages' => 'showPagesString',
            'start_day' => 'startDay',
            'start_on' => 'startOn',
            'status' => 'statusString',
            'sticky' => 'stickyString',
            'stop_before' => 'stopBefore',
            //'uncategorized_entries' => 'uncategorizedEntries',//bool
            'url_title' => 'urlTitleString',
            'username' => 'usernameString',
            'year' => 'year',
            'month' => 'month',
            'day' => 'day',
        ];

        if (strncmp($key, 'search:', 7) === 0) {
            return $this->scopeSearchString($query, substr($key, 7), $value);
        }

        if (! array_key_exists($key, $parameterMap)) {
            return $query;
        }

        $method = 'scope'.ucfirst($parameterMap[$key]);

        return $this->$method($query, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'entry';
    }

    /**
     * Create a new instance of a ChannelData model
     * @return \rsanchez\Deep\Model\ChannelData
     */
    protected function newChannelData()
    {
        $channelData = new ChannelData();

        if ($this->exists) {
            $channelData->exists = true;
            $channelData->entry_id = $this->entry_id;
            $channelData->channel_id = $this->channel_id;
            $channelData->site_id = $this->site_id;
        }

        return $channelData;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomFieldAttributes()
    {
        return $this->channelData ? $this->channelData->getAttributes() : [];
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomFieldAttribute($key, $value)
    {
        if (is_null($this->channelData)) {
            $this->channelData = $this->newChannelData();
        }

        return $this->channelData->$key = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomFieldAttribute($key)
    {
        return $this->channelData && array_key_exists($key, $this->channelData->getAttributes());
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $options = [])
    {
        $saved = parent::save($options);

        if ($saved) {
            $this->saveCategories();
        }

        return $saved;
    }

    /**
     * Dehydrate all custom fields and save to the channel_data table
     *
     * @param  bool $isNew
     * @return void
     */
    protected function saveCustomFields($isNew)
    {
        $entryId = $this->entry_id;

        // it wasn't fetched from DB
        if (is_null($this->channelData)) {
            $this->channelData = $this->newChannelData();
        }

        $this->channelData->exists = ! $isNew;
        $this->channelData->entry_id = $entryId;
        $this->channelData->channel_id = $this->channel_id;
        $this->channelData->site_id = $this->site_id;

        foreach ($this->getProperties() as $field) {
            $name = $field->getName();
            $identifier = $field->getIdentifier();

            $dehydrator = $this->dehydrators->get($field->getType());

            if ($dehydrator) {
                $this->channelData->$identifier = $dehydrator->dehydrate($this, $field);
            } elseif (array_key_exists($name, $this->customFields) && $this->isDataScalar($this->customFields[$name])) {
                $this->channelData->$identifier = $this->dataToScalar($this->customFields[$name]);
            } elseif (! $this->channelData->hasAttribute($identifier)) {
                $this->channelData->$identifier = null;
            }

            if ($field->getType() !== 'date' && ! $this->channelData->hasAttribute('field_ft_'.$field->getId())) {
                $this->channelData->{'field_ft_'.$field->getId()} = 'none';
            }
        }

        $this->channelData->save();

        //restore the original entry ID
        $this->entry_id = $entryId;
    }

    /**
     * Set the categories for this entry
     *
     * @param  \rsanchez\Deep\Collection\CategoryCollection|null $categories
     * @return void
     */
    public function setCategories(CategoryCollection $categories = null)
    {
        $this->setRelation('categories', $categories ?: new CategoryCollection());
    }

    /**
     * Set the categories for this entry by cat_id
     *
     * @param  array $categoryIds
     * @return void
     */
    public function setCategoryIds(array $categoryIds)
    {
        $categories = Category::find($categoryIds);

        $this->setCategories($categories);
    }

    /**
     * Add a category to this entry
     *
     * @param  \rsanchez\Deep\Model\Category $category
     * @return void
     */
    public function addCategory(Category $category)
    {
        $this->categories->push($category);
    }

    /**
     * Add many categories to this entry
     *
     * @param  \rsanchez\Deep\Collection\CategoryCollection $categories
     * @return void
     */
    public function addCategories(CategoryCollection $categories)
    {
        foreach ($categories as $category) {
            $this->categories->push($category);
        }
    }

    /**
     * Add a category to this entry by cat_id
     * @param  int|string $categoryId
     * @return void
     */
    public function addCategoryId($categoryId)
    {
        $category = Category::find($categoryId);

        $this->addCategory($category);
    }

    /**
     * Add many categories to this entry by cat_id
     * @param  array $categoryIds
     * @return void
     */
    public function addCategoryIds(array $categoryIds)
    {
        $categories = Category::find($categoryIds);

        $this->addCategories($categories);
    }

    /**
     * Set the categories attribute
     * @param  \rsanchez\Deep\Collection\CategoryCollection|array|int|string|null $categories
     * @return void
     */
    public function setCategoriesAttribute($categories)
    {
        // array of IDs
        if (is_array($categories)) {
            return $this->setCategoryIds($categories);
        }

        if ($categories instanceof CategoryCollection) {
            return $this->setCategories($categories);
        }

        if (is_int($categories) || preg_match('/^\d+$/', $categories)) {
            return $this->setCategoryIds([$categories]);
        }

        if (is_null($categories)) {
            return $this->setCategories();
        }

        throw new \InvalidArgumentException('$categories must be an array, int, string, or \rsanchez\Deep\Collection\CategoryCollection');
    }

    /**
     * Set the author for this entry
     * @param  \rsanchez\Deep\Model\Member $member
     * @return void
     */
    public function setAuthor(Member $member)
    {
        $this->attributes['author_id'] = $member->member_id;

        $this->setRelation('author', $member);
    }

    /**
     * Set the author_id attribute for this entry
     * @param  $value
     * @return void
     */
    public function setAuthorIdAttribute($value)
    {
        $author = Member::find($value);

        $this->setAuthor($author);
    }

    /**
     * Save selected categories to category_posts
     * @return void
     */
    protected function saveCategories()
    {
        if (! isset($this->relations['categories'])) {
            return;
        }

        $db = $this->getConnection();

        // delete existing
        $db->table('category_posts')
            ->where('entry_id', $this->entry_id)
            ->delete();

        $categoryIds = [];

        foreach ($this->categories as $category) {
            // don't add the same one twice
            if (in_array($category->cat_id, $categoryIds)) {
                continue;
            }

            $categoryIds[] = $category->cat_id;

            $db->table('category_posts')
                ->insert([
                    'cat_id' => $category->cat_id,
                    'entry_id' => $this->entry_id,
                ]);
        }
    }

    /**
     * Filter by Custom Field Search
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  string                                $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, $fieldName, $value)
    {
        $values = array_slice(func_get_args(), 2);

        $model = $this;

        $query->where(function ($subquery) use ($model, $fieldName, $values) {
            foreach ($values as $value) {
                call_user_func([$model, 'scopeOrWhereFieldContains'], $subquery, $fieldName, $value);
            }
        });

        return $query;
    }

    /**
     * Filter by custom field search: string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchString(Builder $query, $fieldName, $string)
    {
        if (! $string) {
            return $query;
        }

        if (! static::getFieldRepository()->hasField($fieldName)) {
            return $query;
        }

        if (preg_match('#^(>|>=|<|<=)(.+)$#', $string, $match)) {
            $comparison = true;
            $operator = $match[1];
            $string = $match[1];
        } else {
            $comparison = false;
        }

        // numeric comparisons are a single value, not a pipe delimited list
        if ($comparison) {
            return $this->scopeWhereField($query, $fieldName, $operator, $string);
        }

        if (strncmp($string, '=', 1) === 0) {
            $contains = false;
            $string = substr($string, 1);
        } else {
            $contains = true;
        }

        if (strncmp($string, 'not ', 4) === 0) {
            $not = true;
            $string = substr($string, 4);
        } else {
            $not = false;
        }

        $and = $contains && strpos($string, '&&') !== false;

        $separator = $and ? '&&' : '|';

        $values = explode($separator, str_replace('IS_EMPTY', '', $string));

        if (! $contains) {
            $method = $not ? 'scopeWhereFieldNotIn' : 'scopeWhereFieldIn';

            return call_user_func([$this, $method], $query, $fieldName, $values);
        }

        if ($and) {
            $method = $not ? 'scopeWhereFieldDoesNotContain' : 'scopeWhereFieldContains';
        } else {
            $method = $not ? 'scopeOrWhereFieldDoesNotContain' : 'scopeOrWhereFieldContains';
        }

        $model = $this;

        return $query->where(function ($subquery) use ($model, $fieldName, $method, $values) {
            foreach ($values as $value) {
                $suffix = '';

                if (preg_match('#^(.+)\\\W$#', $value, $match)) {
                    $value = $match[1];
                    $suffix = 'WholeWord';
                }

                call_user_func([$model, $method.$suffix], $subquery, $fieldName, $value);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function scopeTagparams(Builder $query, array $parameters, array $request = [])
    {
        if (! empty($parameters['orderby'])) {
            $directions = isset($parameters['sort']) ? explode('|', $parameters['sort']) : null;

            foreach (explode('|', $parameters['orderby']) as $i => $column) {
                $direction = isset($directions[$i]) ? $directions[$i] : 'asc';

                if (self::$hydrationEnabled && static::getFieldRepository()->hasField($column)) {
                    $column = 'channel_data.field_id_' . static::getFieldRepository()->getFieldId($column);

                    $query->orderBy($column, $direction);
                } else {
                    $query->orderBy($column, $direction);
                }
            }

            unset($parameters['orderby']);
        }

        if (isset($parameters['dynamic_parameters'])) {
            $this->scopeDynamicParameters(
                $query,
                explode('|', $parameters['dynamic_parameters']),
                $request
            );
        }

        foreach ($parameters as $key => $value) {
            $this->scopeTagparam($query, $key, $value);
        }
    }

    /**
     * Translates a custom field name to field_id_x and performs a where query
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $method the where method to use
     * @param  array                                 $args   the where query arguments
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeWhereFieldHandler(Builder $query, $method, array $args)
    {
        $fieldName = array_shift($args);

        if (static::getFieldRepository()->hasField($fieldName)) {
            $column = 'channel_data.field_id_'.static::getFieldRepository()->getFieldId($fieldName);

            array_unshift($args, $column);

            call_user_func_array([$query, $method], $args);
        }

        return $query;
    }

    /**
     * Order by custom field
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  string                                $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByField(Builder $query, $fieldName, $direction = 'asc')
    {
        if (static::getFieldRepository()->hasField($fieldName)) {
            $column = 'channel_data.field_id_'.static::getFieldRepository()->getFieldId($fieldName);

            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * Where custom field equals
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  string                                $operator
     * @param  mixed                                 $value
     * @param  string                                $boolean
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereField(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'where', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field equals
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  string                                $operator
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereField(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhere', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is between
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  array                                 $values
     * @param  string                                $boolean
     * @param  bool                                  $not
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldBetween(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereBetween', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is between
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  array                                 $values
     * @param  bool                                  $not
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldBetween(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereBetween', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is not between
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  array                                 $values
     * @param  string                                $boolean
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldNotBetween(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereNotBetween', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is not between
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  array                                 $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldNotBetween(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereNotBetween', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  mixed                                 $values
     * @param  string                                $boolean
     * @param  bool                                  $not
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldIn(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereIn', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  mixed                                 $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldIn(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereIn', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is not in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  mixed                                 $values
     * @param  string                                $boolean
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldNotIn(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereNotIn', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is not in
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  mixed                                 $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldNotIn(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereNotIn', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is null
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  string                                $boolean
     * @param  bool                                  $not
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldNull(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereNull', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is null
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldNull(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereNull', array_slice(func_get_args(), 1));
    }

    /**
     * Where custom field is not null
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @param  string                                $boolean
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldNotNull(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'whereNotNull', array_slice(func_get_args(), 1));
    }

    /**
     * Or where custom field is not null
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldNotNull(Builder $query)
    {
        return $this->scopeWhereFieldHandler($query, 'orWhereNotNull', array_slice(func_get_args(), 1));
    }

    /**
     * Translates a custom field name to field_id_x and performs a where like/regexp query
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @param  string                                $boolean
     * @param  bool                                  $not
     * @param  bool                                  $wholeWord
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeWhereFieldContainsHandler(
        Builder $query,
        $fieldName,
        $value,
        $boolean = 'and',
        $not = false,
        $wholeWord = false
    ) {
        if ($value) {
            $operator = $not ? 'not ' : '';

            if ($wholeWord) {
                $operator .= 'regexp';

                $value = '([[:<:]]|^)'.preg_quote($value).'([[:>:]]|$)';

                if (static::getFieldRepository()->hasField($fieldName)) {
                    $column = 'field_id_'.static::getFieldRepository()->getFieldId($fieldName);

                    $method = $boolean === 'and' ? 'whereRaw' : 'orWhereRaw';

                    $tablePrefix = $query->getQuery()->getConnection()->getTablePrefix();

                    $query->$method("`{$tablePrefix}channel_data`.`{$column}` {$operator} '{$value}'");
                }
            } else {
                $operator .= 'like';

                $value = '%'.$value.'%';

                $this->scopeWhereFieldHandler($query, 'where', [$fieldName, $operator, $value, $boolean]);
            }
        } else {
            $operator = $not ? '!=' : '=';

            $this->scopeWhereField($query, $fieldName, $operator, $value, $boolean);
        }

        return $query;
    }

    /**
     * Like scopeWhereFieldContainsHandler, but with many values
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  array                                 $values
     * @param  string                                $boolean
     * @param  bool                                  $not
     * @param  bool                                  $wholeWord
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeWhereFieldContainsManyHandler(
        Builder $query,
        $fieldName,
        array $values,
        $boolean = 'and',
        $not = false,
        $wholeWord = false
    ) {
        if (count($values) === 1) {
            return $this->scopeWhereFieldContainsHandler(
                $query,
                $fieldName,
                current($values),
                $boolean,
                $not,
                $wholeWord
            );
        }

        $model = $this;

        return $query->where(function ($subquery) use ($model, $fieldName, $values, $boolean, $not, $wholeWord) {
            call_user_func(
                [$model, 'scopeWhereFieldContainsHandler'],
                $subquery,
                $fieldName,
                $value,
                $boolean,
                $not,
                $wholeWord
            );
        });
    }

    /**
     * Where field contains
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldContains(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler($query, $fieldName, array_slice(func_get_args(), 2));
    }

    /**
     * Where field does not contain
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldDoesNotContain(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'and',
            true
        );
    }

    /**
     * Or where field contains
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldContains(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler($query, $fieldName, array_slice(func_get_args(), 2), 'or');
    }

    /**
     * Or where field does not contain
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldDoesNotContain(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'or',
            true
        );
    }

    /**
     * Where field contains whole word
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldContainsWholeWord(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'and',
            false,
            true
        );
    }

    /**
     * Where field does not contain whole word
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFieldDoesNotContainWholeWord(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'and',
            true,
            true
        );
    }

    /**
     * Or where field contains whole word
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldContainsWholeWord(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'or',
            false,
            true
        );
    }

    /**
     * Or where field does not contain whole word
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $fieldName
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereFieldDoesNotContainWholeWord(Builder $query, $fieldName, $value)
    {
        return $this->scopeWhereFieldContainsManyHandler(
            $query,
            $fieldName,
            array_slice(func_get_args(), 2),
            'or',
            true,
            true
        );
    }
}
