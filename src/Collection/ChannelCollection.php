<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Channel
 */
class ChannelCollection extends Collection
{
    /**
     * Fields used by this collection
     * @var \rsanchez\Deep\Collection\FieldCollection
     */
    public $fields;
}
