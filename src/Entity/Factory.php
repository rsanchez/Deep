<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Entity\Field\Factory as FieldFactory;
use rsanchez\Deep\Entity\Field\CollectionFactory as FieldCollectionFactory;
use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;

class Factory
{
    protected $fieldFactory;
    protected $fieldCollectionFactory;

    public function __construct(FieldFactory $fieldFactory, FieldCollectionFactory $fieldCollectionFactory)
    {
        $this->fieldFactory = $fieldFactory;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
    }

    public function createEntity(stdClass $row, PropertyCollection $propertyCollection)
    {
        return new Entity($row, $propertyCollection, $this->fieldFactory, $this->fieldCollectionFactory);
    }
}
