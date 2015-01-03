<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Channel;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\Channel
 */
class ChannelCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addChannel($item);
    }

    /**
     * Add a Channel to this collection
     * @param  \rsanchez\Deep\Model\Channel $item
     * @return void
     */
    public function addChannel(Channel $item)
    {
        $this->items[] = $item;
    }
}
