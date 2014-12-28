<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Title;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Collection\AbstractTitleCollection;

/**
 * Collection of \rsanchez\Deep\Model\Title
 */
class TitleCollection extends AbstractTitleCollection
{
    /**
     * Add a Title to this collection
     * @param  \rsanchez\Deep\Model\Title $item
     * @return void
     */
    public function add(Title $item)
    {
        parent::add($item);
    }
}
