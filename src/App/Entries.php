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
 * Static proxy to the Entry model
 */
class Entries extends AbstractProxy
{
    /**
     * {@inheritdoc}
     */
    protected static $accessor = 'Entry';
}
