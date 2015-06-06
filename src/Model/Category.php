<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\CategoryCollection;
use rsanchez\Deep\Collection\CategoryFieldCollection;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;

/**
 * Model for the categories table
 */
class Category extends AbstractEntity
{
    use JoinableTrait, HasCategoryFieldRepositoryTrait, HasChannelRepositoryTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'cat_id';

    /**
     * Whether to build the collection as a nested set
     * @var boolean
     */
    protected $nested = false;

    /**
     * Collection of child categories
     * @var \rsanchez\Deep\Collection\NestedCategoryCollection
     */
    protected $childCategoryCollection;

    /**
     * {@inheritdoc}
     */
    protected $customFieldAttributesRegex = '/^field_(id|ft)_\d+$/';

    /**
     * {@inheritdoc}
     */
    protected $hiddenAttributesRegex = '/^field_(id|ft)_\d+$/';

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'group_id' => 'required|exists:category_groups,group_id',
        'parent_id' => 'exists_or_zero:categories,cat_id',
        'cat_name' => 'required',
        'cat_url_title' => 'required|alpha_dash|unique:categories,cat_url_title',
        'cat_order' => 'required|integer',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributeNames = [
        'site_id' => 'Site ID',
        'group_id' => 'Group ID',
        'parent_id' => 'Parent ID',
        'cat_name' => 'Category Name',
        'cat_url_title' => 'Category URL Title',
        'cat_order' => 'Category Order',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'site_id' => '1',
        'parent_id' => '0',
    ];

    /**
     * Get child categories
     * NOTE: this will be empty unless you call scopeNested
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function getChildrenAttribute()
    {
        if (is_null($this->childCategoryCollection)) {
            $this->childCategoryCollection = new CategoryCollection();
        }

        return $this->childCategoryCollection;
    }

    /**
     * Check if the child collection is empty
     * @return boolean
     */
    public function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    /**
     * Join with category_data
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFields(Builder $query)
    {
        return $this->requireTable($query, 'category_field_data');
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return [
            'category_field_data' => function ($query) {
                $query->join('category_field_data', 'category_field_data.cat_id', '=', 'categories.cat_id');
            },
        ];
    }

    /**
     * Define the entries Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries()
    {
        return $this->belongsToMany('\\rsanchez\\Deep\\Model\\Entry', 'category_posts', 'entry_id', 'cat_id');
    }

    /**
     * Alias custom field names
     *
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        if (! isset($this->attributes[$name]) && static::getCategoryFieldRepository()->hasField($name)) {
            $name = 'field_id_'.static::getCategoryFieldRepository()->getFieldId($name);
        }

        return parent::getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        if (! isset($this->attributes[$name]) && static::getCategoryFieldRepository()->hasField($name)) {
            $name = 'field_id_'.static::getCategoryFieldRepository()->getFieldId($name);
        }

        return parent::setAttribute($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $array = parent::attributesToArray();

        foreach ($this->getProperties() as $property) {
            if (isset($this->customFieldAttributes[$property->getIdentifier()])) {
                $array[$property->getName()] = $this->customFieldAttributes[$property->getIdentifier()];
            } else {
                $array[$property->getName()] = null;
            }
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                        $models
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function newCollection(array $models = [])
    {
        if ($this->nested) {
            $collection = new CategoryCollection();

            $modelsByKey = [];
            $childrenByParentId = [];

            foreach ($models as $model) {
                $modelsByKey[$model->cat_id] = $model;

                if ($model->parent_id) {
                    if (isset($modelsByKey[$model->parent_id])) {
                        $modelsByKey[$model->parent_id]->children->push($model);
                    } else {
                        $childrenByParentId[$model->parent_id][] = $model;
                    }
                } else {
                    if (isset($childrenByParentId[$model->cat_id])) {
                        foreach ($childrenByParentId[$model->cat_id] as $child) {
                            $model->children->push($child);
                        }

                        unset($childrenByParentId[$model->cat_id]);
                    }

                    $collection->push($model);
                }
            }

            $this->nested = false;

            return $collection;
        }

        return new CategoryCollection($models);
    }

    /**
     * Filter by Category ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryId(Builder $query, $categoryId)
    {
        $categoryIds = array_slice(func_get_args(), 1);

        return $query->whereIn('categories.cat_id', $categoryIds);
    }

    /**
     * Filter by not Category ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCategoryId(Builder $query, $categoryId)
    {
        $categoryIds = array_slice(func_get_args(), 1);

        return $query->whereNotIn('categories.cat_id', $categoryIds);
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

        return $query->whereIn('categories.cat_name', $categoryNames);
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

        return $query->whereNotIn('categories.cat_name', $categoryNames);
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
        $groupIds = array_slice(func_get_args(), 1);

        return $query->whereIn('categories.group_id', $groupIds);
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
        $groupIds = array_slice(func_get_args(), 1);

        return $query->whereNotIn('categories.group_id', $groupIds);
    }

    /**
     * Filter by Category ID string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategoryIdString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'CategoryId');
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
     * Parents only, no sub categories
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  bool                                  $parentsOnly
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentOnly(Builder $query, $parentsOnly = true)
    {
        return $parentsOnly ? $query->where('parent_id', 0) : $query;
    }

    /**
     * Parents only string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentOnlyString(Builder $query, $string)
    {
        return $this->scopeParentOnly($query, $string === 'yes');
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

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $groupIds = [];

        $channels->each(function ($channel) use (&$groupIds) {
            $groupIds += $channel->cat_group;
        });

        if ($groupIds) {
            array_unshift($groupIds, $query);

            call_user_func_array([$this, 'scopeCategoryGroup'], $groupIds);
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
        $channelNames = array_slice(func_get_args(), 1);

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $groupIds = [];

        $channels->each(function ($channel) use (&$groupIds) {
            $groupIds += $channel->cat_group;
        });

        if ($groupIds) {
            array_unshift($groupIds, $query);

            call_user_func_array([$this, 'scopeNotCategoryGroup'], $groupIds);
        }

        return $query;
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
     * Filter by Channel Name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $channelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryChannel(Builder $query, $channelName)
    {
        $channelNames = array_slice(func_get_args(), 1);

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $channelIds = [];

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        if ($channelIds) {
            $query->whereHas('entries', function ($q) use ($channelIds) {
                $q->whereIn('channel_titles.channel_id', $channelIds);
            });
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
    public function scopeNotEntryChannel(Builder $query, $channelName)
    {
        $channelNames = array_slice(func_get_args(), 1);

        $channels = static::getChannelRepository()->getChannelsByName($channelNames);

        $channelIds = [];

        $channels->each(function ($channel) use (&$channelIds) {
            $channelIds[] = $channel->channel_id;
        });

        if ($channelIds) {
            $query->whereHas('entries', function ($q) use ($channelIds) {
                $q->whereNotIn('channel_titles.channel_id', $channelIds);
            });
        }

        return $query;
    }

    /**
     * Filter by Channel string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryChannelString(Builder $query, $string)
    {
        return $this->scopeArrayFromString($query, $string, 'EntryChannel');
    }

    /**
     * Filter by categories with no entries
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     * @param  boolean                               $showEmpty
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowEmpty(Builder $query, $showEmpty = true)
    {
        return $showEmpty ? $query : $query->whereHas('entries');
    }

    /**
     * Show empty string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowEmptyString(Builder $query, $string)
    {
        return $this->scopeShowEmpty($query, $string === 'yes');
    }

    /**
     * Filter by expired entries
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     * @param  boolean                               $showExpired
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExpired(Builder $query, $showExpired = true)
    {
        if ($showExpired) {
            return $query;
        }

        $prefix = $query->getQuery()->getConnection()->getTablePrefix();

        return $query->whereHas('entries', function ($q) {
            $q->whereRaw(
                "(`{$prefix}channel_titles`.`expiration_date` = '' OR  `{$prefix}channel_titles`.`expiration_date` > NOW())"
            );
        });
    }

    /**
     * Show expired string parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExpiredString(Builder $query, $string)
    {
        return $this->scopeShowExpired($query, $string === 'yes');
    }

    public function scopeShowFutureEntries(Builder $query, $showFutureEntries = true)
    {
        if ($showFutureEntries) {
            return $query;
        }

        $prefix = $query->getQuery()->getConnection()->getTablePrefix();

        return $query->whereHas('entries', function ($q) {
            $q->whereRaw(
                "(`{$prefix}channel_titles`.`expiration_date` = '' OR  `{$prefix}channel_titles`.`expiration_date` > NOW())"
            );
        });
    }

    /**
     * Show empty string parameter
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
     * Filter by Entry Status
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $status)
    {
        $statuses = array_slice(func_get_args(), 1);

        return $query->whereHas('entries', function ($q) use ($statuses) {
            return $q->whereIn('status', $statuses);
        });
    }

    /**
     * Filter by Not Entry Status
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  dynamic  string                       $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotStatus(Builder $query, $status)
    {
        $statuses = array_slice(func_get_args(), 1);

        return $query->whereHas('entries', function ($q) use ($statuses) {
            return $q->whereNotIn('status', $statuses);
        });
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
            'show' => 'categoryIdString',
            'category_name' => 'categoryNameString',
            'category_group' => 'categoryGroupString',
            'channel' => 'channelString',
            'limit' => 'limit',
            'offset' => 'offset',
            'parent_only' => 'parentOnlyString',
            //'restrict_channel' => 'restrictChannel',
            'show_empty' => 'showEmptyString',
            'show_expired' => 'showExpiredString',
            'show_future_entries' => 'showFutureEntriesString',
            'status' => 'statusString',
            'style' => 'styleString',
        ];

        if (! array_key_exists($key, $parameterMap)) {
            return $query;
        }

        $method = 'scope'.ucfirst($parameterMap[$key]);

        return $this->$method($query, $value);
    }

    /**
     * Apply an array of parameters
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $parameters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTagparams(Builder $query, array $parameters)
    {
        $showEmpty = ! isset($parameters['show_empty']) || $parameters['show_empty'] === 'yes';
        $restrictChannel = ! isset($parameters['restrict_channel']) || $parameters['restrict_channel'] === 'yes';

        if ($showEmpty) {
            unset($parameters['status'], $parameters['show_expired'], $parameters['show_future_entries']);
        } else {
            if ($restrictChannel && isset($parameters['channel'])) {
                $query->entryChannelString($parameters['channel']);
            }
        }

        // because you're so special
        if (! empty($parameters['orderby'])) {
            $directions = isset($parameters['sort']) ? explode('|', $parameters['sort']) : null;

            foreach (explode('|', $parameters['orderby']) as $i => $column) {
                $direction = isset($directions[$i]) ? $directions[$i] : 'asc';
                $query->orderBy($column, $direction);
            }
        }

        foreach ($parameters as $key => $value) {
            $this->scopeTagparam($query, $key, $value);
        }

        return $query;
    }

    /**
     * Set the "style" of the results, either 'nested' or 'linear'
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStyleString(Builder $query, $string)
    {
        return $string === 'nested' ? $this->scopeNested($query) : $query;
    }

    /**
     * Order by Category Nesting
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNested(Builder $query)
    {
        $this->nested = true;

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function saveCustomFields($isNew)
    {
        $categoryData = $this->customFieldAttributes;

        $categoryData['site_id'] = $this->site_id;
        $categoryData['group_id'] = $this->group_id;

        foreach ($this->getFields() as $field) {
            if (array_key_exists($field->getName(), $this->customFields)) {
                $categoryData['field_id_'.$field->getId()] = $this->customFields[$field->getName()];
            }
        }

        $query = $this->getConnection()->table('category_field_data');

        if ($isNew) {
            $categoryData['cat_id'] = $this->cat_id;

            $query->insert($categoryData);
        } else {
            $query->where('cat_id', $this->cat_id)
                ->update($categoryData);
        }
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
     * {@inheritdoc}
     */
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = $this->getDefaultValidationRules($validatorFactory, $property);

        $rules['cat_url_title'] .= sprintf(',%s,cat_id', $this->cat_id);

        return $rules;
    }

    /**
     * Get all the category custom fields
     *
     * @return \rsanchez\Deep\Collection\CategoryFieldCollection
     */
    public function getFields()
    {
        if (! static::getCategoryFieldRepository()) {
            return new CategoryFieldCollection();
        }

        return static::getCategoryFieldRepository()->getFieldsByGroup($this->group_id);
    }

    /**
     * Get the entity ID (eg. entry_id or row_id)
     * @return string|int
     */
    public function getId()
    {
        return $this->cat_id;
    }

    /**
     * Get the entity type (eg. 'matrix' or 'grid' or 'entry')
     * @return string|null
     */
    public function getType()
    {
        return 'category';
    }

    /**
     * Get the entity prefix (eg. 'entry' or 'row')
     * @return string|null
     */
    public function getPrefix()
    {
        return 'cat';
    }

    /**
     * Get collection of AbstractProperties
     * @return \rsanchez\Deep\Collection\PropertyCollection
     */
    public function getProperties()
    {
        return $this->getFields();
    }
}
