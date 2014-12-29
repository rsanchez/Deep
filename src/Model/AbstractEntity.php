<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the channel entries, matrix rows and grid rows
 */
abstract class AbstractEntity extends Model
{
    /**
     * Get the entity ID (eg. entry_id or row_id)
     * @return string|int
     */
    abstract public function getId();

    /**
     * Get the entity type (eg. 'matrix' or 'grid' or 'entry')
     * @return string|null
     */
    abstract public function getType();

    /**
     * Get the entity prefix (eg. 'entry' or 'row')
     * @return string|null
     */
    abstract public function getPrefix();
}
