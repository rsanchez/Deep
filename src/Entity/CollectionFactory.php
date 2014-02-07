<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Entity\Collection;
use rsanchez\Deep\Entity\Factory as EntityFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Property\CollectionFactoryInterface as PropertyCollectionFactory;

class CollectionFactory
{
    /**
     * @var rsanchez\Deep\Entity\Factory
     */
    protected $factory;

    /**
     * @var rsanchez\Deep\Fieldtype\Repository
     */
    protected $fieldtypeRepository;

    /**
     * @var rsanchez\Deep\Fieldtype\CollectionFactory
     */
    protected $fieldtypeCollectionFactory;

    /**
     * @var rsanchez\Deep\Property\CollectionFactoryInterface
     */
    protected $propertyCollectionFactory;

    public function __construct(
        EntityFactory $factory,
        FieldtypeRepository $fieldtypeRepository,
        FieldtypeCollectionFactory $fieldtypeCollectionFactory,
        PropertyCollectionFactory $propertyCollectionFactory
    ) {
        $this->factory = $factory;
        $this->fieldtypeRepository= $fieldtypeRepository;
        $this->fieldtypeCollectionFactory = $fieldtypeCollectionFactory;
        $this->propertyCollectionFactory = $propertyCollectionFactory;
    }

    public function createCollection()
    {
        return new Collection(
            $this->factory,
            $this->fieldtypeRepository,
            $this->fieldtypeCollectionFactory,
            $this->propertyCollectionFactory
        );
    }
}
