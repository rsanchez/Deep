<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MemberField;

/**
 * Collection of \rsanchez\Deep\Model\MemberField
 */
class MemberFieldCollection extends AbstractFieldCollection
{
    /**
     * Add a MemberField to this collection
     * @param  \rsanchez\Deep\Model\MemberField $item
     * @return void
     */
    public function add(MemberField $item)
    {
        parent::add($item);
    }
}
