<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixRow;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\MatrixRow
 */
class MatrixRowCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\MatrixRow';
}
