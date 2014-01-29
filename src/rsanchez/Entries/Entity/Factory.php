<?php

namespace rsanchez\Entries\Entity;

use rsanchez\Entries\Entity\Field\Factory as FieldFactory;
use rsanchez\Entries\Entity\Field\CollectionFactory as FieldCollectionFactory;

abstract class Factory
{
    protected $fieldFactory;
    protected $fieldCollectionFactory;

    public function __construct(FieldFactory $fieldFactory, FieldCollectionFactory $fieldCollectionFactory)
    {
        $this->fieldFactory = $fieldFactory;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
    }
}
