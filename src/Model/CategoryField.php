<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Model\AbstractField;
use rsanchez\Deep\Collection\CategoryFieldCollection;

/**
 * Model for the category_fields table
 */
class CategoryField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'category_fields';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'field_id';

    /**
     * {@inheritdoc}
     *
     * @param  array                                             $fields
     * @return \rsanchez\Deep\Collection\CategoryFieldCollection
     */
    public function newCollection(array $fields = array())
    {
        return new CategoryFieldCollection($fields);
    }
}
