<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

/**
 * {@inheritdoc}
 *
 * A model collection that is sortable and filterable by common parameters
 */
abstract class AbstractFilterableCollection extends Collection
{
    /**
     * Create a copy of this Collection
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function createClone()
    {
        return clone $this;
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
     * @param  string $attribute name of the attribute on which to filter
     * @param  string $filter   a string describing the filter
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function filterByAttribute($attribute, $filter)
    {
        // numeric comparisons must be a a single value, not a pipe delimited list
        if (preg_match('#^(>|>=|<|<=)(.+)$#', $filter, $match)) {
            $operator = $match[1];

            $filter = $match[2];

            $this->items = array_filter($this->items, function ($model) use ($filter, $attribute, $operator) {
                switch ($operator) {
                    case '>':
                        return $model->$attribute > $filter;
                    case '>=':
                        return $model->$attribute >= $filter;
                    case '<':
                        return $model->$attribute < $filter;
                    case '<=':
                        return $model->$attribute <= $filter;
                }
            });

            return $this;
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

        $values = explode($separator, $filter);

        if ($contains) {

            $this->items = array_filter($this->items, function ($model) use ($attribute, $and, $not, $values) {
                $isMatch = false;

                foreach ($values as $value) {
                    if ($value === 'IS_EMPTY') {
                        $isValueMatch = empty($model->$attribute);
                    } else {
                        if (preg_match('#^(.+)\\\W$#', $value, $match)) {
                            $regex = '#\b'.preg_quote($match[1]).'\b#';
                        } else {
                            $regex = '#'.preg_quote($value).'#';
                        }

                        $isValueMatch = (bool) preg_match($regex, $model->$attribute);
                    }

                    $isMatch = $isValueMatch || $not;

                    if ($and && ! $isValueMatch) {
                        break;
                    } elseif (! $and && $isValueMatch) {
                        break;
                    }
                }

                return $isMatch;
            });

        } else {
            $this->items = array_filter($this->items, function ($model) use ($attribute, $not, $values) {
                return in_array($model->$attribute, $values) || $not;
            });
        }

        return $this;
    }

    /**
     * Filter by model ID
     *
     * @param  int $id,... one or more IDs
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function filterById($id)
    {
        $ids = is_array($id) ? $id : array($id);

        $this->items = array_filter($this->items, function ($model) use ($ids) {
            return in_array($model->getKey(), $ids);
        });

        return $this;
    }

    /**
     * Limit the collection
     *
     * @param  int $limit
     * @param  int $offset
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function limit($limit, $offset = 0)
    {
        $this->items = array_slice($this->items, $offset, $limit);

        return $this;
    }

    /**
     * Offset the collection
     *
     * @param  int $limit
     * @param  int $offset
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function offset($offset, $limit = null)
    {
        $this->items = array_slice($this->items, $offset, $limit);

        return $this;
    }

    /**
     * Sort by one or more model attributes
     *
     * @param  array|int    $id one or more IDs
     * @param  array|string $sort sort direction
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function sortByAttribute($attribute, $sort = 'asc')
    {
        //multisort
        $attributes = is_array($attribute) ? $attribute : array($attribute);

        $sort = is_array($sort) ? $sort : array($sort);

        $sort = array_pad($sort, count($attributes), 'asc');

        $comparison = array();

        foreach ($this->items as $i => $model) {
            foreach ($attributes as $attribute) {
                $comparison[$attribute][$i] = $model->$attribute;
            }
        }

        $args = array();

        foreach ($attributes as $i => $attribute) {
            $args[] = $comparison[$attribute];
            $args[] = $sort[$i] === 'asc' ? SORT_ASC : SORT_DESC;
        }

        $args[] =& $this->items;

        call_user_func_array('array_multisort', $args);

        return $this;
    }

    /**
     * Sort by model ID in the specified order
     *
     * @param  int $id,... one or more IDs
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
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
     * @param  array $params
     * @return \rsanchez\Deep\Collection\AbstractFilterableCollection
     */
    public function __invoke(array $params)
    {
        if (! $this->items) {
            return $this;
        }

        $collection = $this->createClone();

        $ignore = array('fixed_order', 'orderby', 'sort', 'offset', 'limit');

        $filters = array_diff_key($params, array_flip($ignore));

        $searches = array_filter(array_keys($params), function ($key) {
            return;
        });

        foreach ($filters as $attribute => $filter) {
            if (! $filter) {
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

            $sort = isset($params['sort']) ? explode('|', $params['sort']) : array();

            $collection->sortByAttribute($attributes, $sort);
        }

        if (isset($params['offset']) || isset($params['limit'])) {
            $offset = isset($params['offset']) ? $params['offset'] : 0;
            $limit = isset($params['limit']) ? $params['limit'] : null;

            $collection->limit($limit, $offset);
        }

        return $collection;
    }
}
