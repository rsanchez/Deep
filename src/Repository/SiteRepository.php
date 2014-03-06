<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\SiteCollection;

/**
 * Repository of all Sites
 */
class SiteRepository
{
    /**
     * Collection of all Channels
     * @var \rsanchez\Deep\Collection\SiteCollection
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Collection\SiteCollection $collection
     */
    public function __construct(SiteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Get Collection of all Sites
     *
     * @return \rsanchez\Deep\Collection\SiteCollection
     */
    public function getSites()
    {
        return $this->collection;
    }
}
