<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\SiteCollection;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Repository\AbstractDeferredRepository;

/**
 * Repository of all Sites
 */
class SiteRepository extends AbstractDeferredRepository
{
    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Model\Site $model
     */
    public function __construct(Site $model)
    {
        parent::__construct($model);
    }

    /**
     * Get Collection of all Sites
     *
     * @return \rsanchez\Deep\Collection\SiteCollection
     */
    public function getSites()
    {
        $this->boot();

        return $this->collection;
    }
}
