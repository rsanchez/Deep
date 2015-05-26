<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

/**
 * {@inheritdoc}
 *
 * A model collection that is sortable and filterable by common parameters
 */
trait FilterableTrait
{
    /**
     * Create a copy of this Collection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createClone()
    {
        return clone $this;
    }

    /**
     * Filter by model attribute contains
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  array                                    $values
     * @param  bool                                     $and
     * @param  bool                                     $not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeContains($attribute, array $values, $and = false, $not = false)
    {
        $this->items = array_filter($this->items, function ($model) use ($attribute, $and, $not, $values) {
            $isMatch = false;

            foreach ($values as $value) {
                if ($value) {
                    if (preg_match('#^(.+)\\\W$#', $value, $match)) {
                        $regex = '#\b'.preg_quote($match[1]).'\b#';
                    } else {
                        $regex = '#'.preg_quote($value).'#';
                    }

                    $isValueMatch = (bool) preg_match($regex, $model->$attribute);
                } else {
                    $isValueMatch = empty($model->$attribute);
                }

                $isMatch = $not ? ! $isValueMatch : $isValueMatch;

                if ($and && ! $isValueMatch) {
                    break;
                } elseif (! $and && $isValueMatch) {
                    break;
                }
            }

            return $isMatch;
        });
    }

    /**
     * Filter by model attribute in array string
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  string                                   $filter    pipe-delimited list of values, optionaly prefixed by not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeInString($attribute, $filter)
    {
        $not = strncmp('not ', $filter, 4) === 0;

        if ($not) {
            $filter = substr($filter, 4);
        }

        return $this->filterByAttributeIn($attribute, explode('|', $filter), $not);
    }

    /**
     * Filter by model attribute in array
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  array                                    $values
     * @param  bool                                     $not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeIn($attribute, array $values, $not = false)
    {
        $this->items = array_filter($this->items, function ($model) use ($attribute, $not, $values) {
            return $not ? ! in_array($model->$attribute, $values) : in_array($model->$attribute, $values);
        });
    }

    /**
     * Filter by model attribute numerical comparison
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  mixed                                    $value
     * @param  string                                   $operator  >, >=, <, <=
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeComparison($attribute, $value, $operator)
    {
        $this->items = array_filter($this->items, function ($model) use ($value, $attribute, $operator) {
            switch ($operator) {
                case '>':
                    return $model->$attribute > $value;
                case '>=':
                    return $model->$attribute >= $value;
                case '<':
                    return $model->$attribute < $value;
                case '<=':
                    return $model->$attribute <= $value;
            }
        });

        return $this;
    }

    /**
     * Filter by model attribute
     *
     * The filter should be one of the following formats:
     * - 'foo|bar'
     * - 'not foo|bar'
     * - '=foo|bar'
     * - '=not foo|bar'
     * - 'foo&&bar'
     * - 'not foo|bar'
     * - 'foo\W|bar'
     * - '>=3'
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  string                                   $filter    a string describing the filter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttribute($attribute, $filter)
    {
        // numeric comparisons must be a a single value, not a pipe delimited list
        if (preg_match('#^(>|>=|<|<=)(.+)$#', $filter, $match)) {
            $operator = $match[1];

            $value = $match[2];

            return $this->filterByAttributeComparison($attribute, $value, $operator);
        }

        if (strncmp($filter, '=', 1) === 0) {
            $contains = false;
            $filter = substr($filter, 1);
        } else {
            $contains = true;
        }

        if (strncmp($filter, 'not ', 4) === 0) {
            $not = true;
            $filter = substr($filter, 4);
        } else {
            $not = false;
        }

        $and = $contains && strpos($filter, '&&') !== false;

        $separator = $and ? '&&' : '|';

        $filter = str_replace('IS_EMPTY', '', $filter);

        $values = explode($separator, $filter);

        if ($contains) {
            return $this->filterByAttributeContains($attribute, $values, $and, $not);
        }

        return $this->filterByAttributeIn($attribute, $values, $not);
    }

    /**
     * Filter by model ID
     *
     * @param  int                                      $id,... one or more IDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterById($id)
    {
        $ids = is_array($id) ? $id : [$id];

        $this->items = array_filter($this->items, function ($model) use ($ids) {
            return in_array($model->getKey(), $ids);
        });

        return $this;
    }

    /**
     * Limit the collection
     *
     * @param  int                                      $limit
     * @param  int                                      $offset
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function limit($limit, $offset = 0)
    {
        $this->items = array_slice($this->items, $offset, $limit);

        return $this;
    }

    /**
     * Offset the collection
     *
     * @param  int                                      $limit
     * @param  int                                      $offset
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function offset($offset, $limit = null)
    {
        $this->items = array_slice($this->items, $offset, $limit);

        return $this;
    }

    /**
     * Sort by one or more model attributes
     *
     * @param  array|int                                $id   one or more IDs
     * @param  array|string                             $sort sort direction
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sortByAttribute($attribute, $sort = 'asc')
    {
        //multisort
        $attributes = is_array($attribute) ? $attribute : [$attribute];

        $sort = is_array($sort) ? $sort : [$sort];

        $sort = array_pad($sort, count($attributes), 'asc');

        $comparison = [];

        foreach ($this->items as $i => $model) {
            foreach ($attributes as $attribute) {
                $comparison[$attribute][$i] = $model->$attribute;
            }
        }

        $args = [];

        foreach ($attributes as $i => $attribute) {
            $args[] = $comparison[$attribute];
            $args[] = $sort[$i] === 'asc' ? SORT_ASC : SORT_DESC;
        }

        $args[] = & $this->items;

        call_user_func_array('array_multisort', $args);

        return $this;
    }

    /**
     * Sort by model ID in the specified order
     *
     * @param  int                                      $id,... one or more IDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sortByFixedOrder($id)
    {
        $ids = is_array($id) ? $id : func_get_args();

        $this->filterById($ids);

        usort($this->items, function ($modelA, $modelB) use ($ids) {
            return array_search($modelA->getKey(), $ids) - array_search($modelB->getKey(), $ids);
        });

        return $this;
    }

    /**
     * Sort and filter a clone of this collection according to the given array of params.
     *
     * The array may contain the following:
     * - limit
     * - offset
     * - search:your_field
     * - fixed_order
     * - orderby
     * - sort
     * - any model attribute (ex. row_id)
     *
     * @param  array                                    $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagparams(array $params)
    {
        if (! $this->items) {
            return $this;
        }

        $collection = $this->createClone();

        $ignore = ['fixed_order', 'orderby', 'sort', 'offset', 'limit', 'var_prefix', 'backspace'];

        $filters = array_diff_key($params, array_flip($ignore));

        $searches = array_filter(array_keys($params), function ($key) {
            return;
        });

        foreach ($filters as $attribute => $filter) {
            if (! $filter) {
                continue;
            }

            $method = 'filterBy'.ucfirst(camel_case($attribute));

            if (method_exists($collection, $method)) {
                $collection->$method($filter);
                continue;
            }

            if (strncmp($attribute, 'search:', 7) === 0) {
                $attribute = substr($attribute, 7);
            } elseif (strncmp($filter, '=', 1) !== 0) {
                $filter = '='.$filter;
            }

            $collection->filterByAttribute($attribute, $filter);
        }

        if (isset($params['fixed_order'])) {
            $ids = explode('|', $params['fixed_order']);

            $collection->sortByFixedOrder($ids);
        } elseif (isset($params['orderby'])) {
            $attributes = explode('|', $params['orderby']);

            $sort = isset($params['sort']) ? explode('|', $params['sort']) : [];

            $collection->sortByAttribute($attributes, $sort);
        }

        if (isset($params['offset']) || isset($params['limit'])) {
            $offset = isset($params['offset']) ? $params['offset'] : 0;
            $limit = isset($params['limit']) ? $params['limit'] : null;

            $collection->limit($limit, $offset);
        }

        return $collection;
    }

    /**
     * Get the first Model's given attribute
     * @param  string     $name
     * @return mixed|null
     */
    public function __get($name)
    {
        $model = $this->first();

        return $model ? $model->$name : null;
    }

    /**
     * Alias to tagparams
     *
     * @param  array                                    $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(array $params)
    {
        return $this->tagparams($params);
    }
}
