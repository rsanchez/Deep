<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Repository\FieldRepository;
use Carbon\Carbon;

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
     * Set the global FieldRepository
     * @param  \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepository $fieldRepository)
    {
        self::$fieldRepository = $fieldRepository;
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

        $query->join('channel_data', 'channel_titles.entry_id', '=', 'channel_data.entry_id');

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function newCollection(array $models = array())
    {
        $method = "{$this->collectionClass}::createWithFields";

        $collection = call_user_func($method, $models, self::$channelRepository, self::$fieldRepository);

        if ($models) {
            $this->hydrateCollection($collection);
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
            if ($attributes[$key] instanceof Carbon) {
                $attributes[$key] = (string) $attributes[$key];
            }
        }

        return $this->getArrayableItems($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $array = parent::toArray();

        // remove field_id_X fields from the array
        foreach ($this->attributes as $key => $value) {
            if (preg_match('#^field_(id|dt|ft)_#', $key)) {
                unset($array[$key]);
            }
        }

        $this->channel->fields->each(function ($field) use (&$array) {
            if (isset($array[$field->field_name]) && method_exists($array[$field->field_name], 'toArray')) {
                $array[$field->field_name] = $array[$field->field_name]->toArray();
            }
        });

        return $array;
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
                call_user_func(array($model, 'scopeOrWhereFieldContains'), $subquery, $fieldName, $value);
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

        if (! self::$fieldRepository->hasField($fieldName)) {
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

            return call_user_func_array(array($this, $method), array($query, $fieldName, $values));
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

                call_user_func(array($model, $method.$suffix), $subquery, $fieldName, $value);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function scopeTagparams(Builder $query, array $parameters, array $request = array())
    {
        if (! empty($parameters['orderby'])) {
            $directions = isset($parameters['sort']) ? explode('|', $parameters['sort']) : null;

            foreach (explode('|', $parameters['orderby']) as $i => $column) {
                $direction = isset($directions[$i]) ? $directions[$i] : 'asc';

                if (self::$fieldRepository->hasField($column)) {
                    $column = 'channel_data.field_id_'.self::$fieldRepository->getFieldId($column);

                    $query->orderBy($column, $direction);
                } else {
                    $query->orderBy($column, $direction);
                }
            }

            unset($parameters['orderby']);
        }

        return parent::scopeTagparams($query, $parameters, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function scopeTagparam(Builder $query, $key, $value)
    {
        if (strncmp($key, 'search:', 7) === 0) {
            return $this->scopeSearchString($query, substr($key, 7), $value);
        }

        return parent::scopeTagparam($query, $key, $value);
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
            $column = 'channel_data.field_id_'.self::$fieldRepository->getFieldId($fieldName);

            array_unshift($args, $column);

            call_user_func_array(array($query, $method), $args);
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
        if (self::$fieldRepository->hasField($fieldName)) {
            $column = 'channel_data.field_id_'.self::$fieldRepository->getFieldId($fieldName);

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

                if (self::$fieldRepository->hasField($fieldName)) {
                    $column = 'field_id_'.self::$fieldRepository->getFieldId($fieldName);

                    $method = $boolean === 'and' ? 'whereRaw' : 'orWhereRaw';

                    $tablePrefix = $query->getQuery()->getConnection()->getTablePrefix();

                    $query->$method("`{$tablePrefix}channel_data`.`{$column}` {$operator} '{$value}'");
                }
            } else {
                $operator .= 'like';

                $value = '%'.$value.'%';

                $this->scopeWhereFieldHandler($query, 'where', array($fieldName, $operator, $value, $boolean));
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
                array($model, 'scopeWhereFieldContainsHandler'),
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
