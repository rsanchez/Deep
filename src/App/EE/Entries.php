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
 * Static proxy to the Entry model
 */
class Entries extends AbstractProxy
{
    /**
     * {@inheritdoc}
     */
    protected static function getAccessor()
    {
        return 'Entry';
    }
}
