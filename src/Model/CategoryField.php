<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'field_id_'.$this->field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->field_type;
    }
}
