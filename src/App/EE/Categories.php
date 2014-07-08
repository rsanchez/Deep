<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\App\EE;

use rsanchez\Deep\App\EE\AbstractProxy;

/**
 * Static proxy to the Category model
 */
class Categories extends AbstractProxy
{
    /**
     * {@inheritdoc}
     */
    protected static function getAccessor()
    {
        return 'Category';
    }
}
