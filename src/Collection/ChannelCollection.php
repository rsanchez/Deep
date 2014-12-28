<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Channel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Channel
 */
class ChannelCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a Channel to this collection
     * @param  \rsanchez\Deep\Model\Channel $item
     * @return void
     */
    public function add(Channel $item)
    {
        $this->items[] = $item;
    }
}
