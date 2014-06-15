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
 * Collection of \rsanchez\Deep\Model\GridRow
 */
class GridRowCollection extends Collection implements FilterableInterface
{
    use FilterableTrait;
}
