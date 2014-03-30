<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\App;

use rsanchez\Deep\App\AbstractProxy;

/**
 * Static proxy to the Category model
 */
class Categories extends AbstractProxy
{
    /**
     * {@inheritdoc}
     */
    protected static $accessor = 'Category';
}
