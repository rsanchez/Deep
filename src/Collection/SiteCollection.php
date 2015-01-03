<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Site;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\Site
 */
class SiteCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addSite($item);
    }

    /**
     * Add a Site to this collection
     * @param  \rsanchez\Deep\Model\Site $item
     * @return void
     */
    public function addSite(Site $item)
    {
        $this->items[] = $item;
    }
}
