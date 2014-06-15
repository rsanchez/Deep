<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Collection\FilterableTrait;
use rsanchez\Deep\Collection\FilterableInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Category
 */
class CategoryCollection extends Collection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * Filter by cat_id attribute
     *
     * @param  string                                       $filter pipe-delimited list of cat_ids, optionaly prefixed by not
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function filterByShow($filter)
    {
        return $this->filterByAttributeInString('cat_id', $filter);
    }

    /**
     * Filter by group_id attribute
     *
     * @param  string                                       $filter pipe-delimited list of group_ids, optionaly prefixed by not
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function filterByShowGroup($filter)
    {
        return $this->filterByAttributeInString('group_id', $filter);
    }
}
