<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Repository\AbstractFieldRepository;

/**
 * Repository of all CategoryFields
 */
class CategoryFieldRepository extends AbstractFieldRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(CategoryField $model)
    {
        parent::__construct($model);
    }
}
