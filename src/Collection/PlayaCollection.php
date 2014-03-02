<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Collection\EntryCollection;

/**
 * Collection of \rsanchez\Deep\Model\PlayaEntry
 */
class PlayaCollection extends EntryCollection
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        // flatten the array keys
        return array_values(parent::toArray());
    }
}
