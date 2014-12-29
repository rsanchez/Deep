<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\App;

/**
 * Static proxy to the Title model
 */
class Titles extends AbstractProxy
{
    /**
     * {@inheritdoc}
     */
    protected static function getAccessor()
    {
        return 'Title';
    }
}
