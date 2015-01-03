<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MemberField;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\MemberField
 */
class MemberFieldCollection extends AbstractFieldCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addMemberField($item);
    }

    /**
     * Add a MemberField to this collection
     * @param  \rsanchez\Deep\Model\MemberField $item
     * @return void
     */
    public function addMemberField(MemberField $item)
    {
        $this->addField($item);
    }
}
