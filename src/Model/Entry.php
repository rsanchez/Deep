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
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Hydrator\HydratorFactory;
use rsanchez\Deep\Collection\AbstractTitleCollection;
use DateTime;
use DateTimeZone;

/**
 * Model for the channel_titles table, joined with channel_data
 */
class Entry extends Title
{
    /**
     * The class used when creating a new Collection
     * @var string
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\EntryCollection';

    /**
     * Global Field Repository
     * @var \rsanchez\Deep\Repository\FieldRepository
     */
    public static $fieldRepository;

    /**
     * Hydrator Factory
     * @var \rsanchez\Deep\Hydrator\Factory
     */
    public static $hydratorFactory;

    /**
     * Set the global FieldRepository
     * @param  \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepository $fieldRepository)
    {
        self::$fieldRepository = $fieldRepository;
    }

    /**
     * Set the global HydratorFactory
     * @param  \rsanchez\Deep\Repository\HydratorFactory $hydratorFactory
     * @return void
     */
    public static function setHydratorFactory(HydratorFactory $hydratorFactory)
    {
        self::$hydratorFactory = $hydratorFactory;
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

        $query->addSelect('channel_data.*');

        $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');

        return $query;
    }

    /**
     * Loop through all the hydrators to set Entry custom field attributes
     * @return void
     */
    public function hydrateCollection(AbstractTitleCollection $collection)
    {
        parent::hydrateCollection($collection);

        $collection->fields = new FieldCollection();

        foreach ($collection->channels as $channel) {

            $fields = self::$fieldRepository->getFieldsByGroup($channel->field_group);

            foreach ($fields as $field) {
                $collection->addField($field);
            }

        }

        $hydrators = self::$hydratorFactory->getHydrators($collection);

        // loop through the hydrators for preloading
        foreach ($hydrators as $hydrator) {
            $hydrator->preload($collection->getEntryIds());
        }

        // loop again to actually hydrate
        foreach ($collection as $entry) {
            foreach ($hydrators as $hydrator) {
                $hydrator->hydrate($entry);
            }
        }
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

        $search = array();

        foreach ($parameters as $key => $value) {
            if (strncmp($key, 'search:', 7) === 0) {
                $key = 'search';
                $search[substr($key, 7)] = explode('|', $value);
                continue;
            }

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

        if ($search) {
            $this->scopeSearch($query, $search);
        }

        return $query;
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

        if (self::$fieldRepository->hasField($fieldName)) {
            $column = 'field_id_'.self::$fieldRepository->getFieldId($fieldName);

            array_unshift($column, $args);

            call_user_func_array(array($query, $method), $args);
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
}
