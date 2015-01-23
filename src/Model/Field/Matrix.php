<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model\Field;

use rsanchez\Deep\Model\Field;

/**
 * Model for the Matrix fieldtype
 */
class Matrix extends Field
{
    /**
     * {@inheritdoc}
     */
    public function hasChildProperties()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildProperties()
    {
        return $this->cols;
    }

    /**
     * Define the matrix_cols Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasOneFromRepository
     */
    public function cols()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\MatrixCol', 'field_id', 'field_id');
    }
}
