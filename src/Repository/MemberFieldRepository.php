<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Repository\AbstractFieldRepository;

/**
 * Repository of all MemberFields
 */
class MemberFieldRepository extends AbstractFieldRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(MemberField $model)
    {
        parent::__construct($model);
    }
}
