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
interface FilterableInterface
{
    /**
     * Create a copy of this Collection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createClone();

    /**
     * Filter by model attribute contains
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  array                                    $values
     * @param  bool                                     $and
     * @param  bool                                     $not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeContains($attribute, array $values, $and = false, $not = false);

    /**
     * Filter by model attribute in array string
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  string                                   $filter    pipe-delimited list of values, optionaly prefixed by not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeInString($attribute, $filter);

    /**
     * Filter by model attribute in array
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  array                                    $values
     * @param  bool                                     $not
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeIn($attribute, array $values, $not = false);

    /**
     * Filter by model attribute numerical comparison
     *
     * @param  string                                   $attribute name of the attribute on which to filter
     * @param  mixed                                    $value
     * @param  string                                   $operator  >, >=, <, <=
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByAttributeComparison($attribute, $value, $operator);

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
    public function filterByAttribute($attribute, $filter);

    /**
     * Filter by model ID
     *
     * @param  int                                      $id,... one or more IDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterById($id);

    /**
     * Limit the collection
     *
     * @param  int                                      $limit
     * @param  int                                      $offset
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function limit($limit, $offset = 0);

    /**
     * Offset the collection
     *
     * @param  int                                      $limit
     * @param  int                                      $offset
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function offset($offset, $limit = null);

    /**
     * Sort by one or more model attributes
     *
     * @param  array|int                                $id   one or more IDs
     * @param  array|string                             $sort sort direction
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sortByAttribute($attribute, $sort = 'asc');

    /**
     * Sort by model ID in the specified order
     *
     * @param  int                                      $id,... one or more IDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sortByFixedOrder($id);

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
    public function tagparams(array $params);

    /**
     * Alias to tagparams
     *
     * @param  array                                    $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(array $params);
}
