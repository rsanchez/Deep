<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Title;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Title
 */
class TitleCollection extends AbstractTitleCollection
{
    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\Title';
}
