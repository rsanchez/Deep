<?php

namespace rsanchez\Deep\Common\Entity;

use rsanchez\Deep\Common\Entity\AbstractEntity;
use rsanchez\Deep\Common\Entity\AbstractFactory as EntityFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Common\Property\CollectionFactoryInterface as PropertyCollectionFactory;

abstract class AbstractCollectionFactory
{
    /**
     * @var rsanchez\Deep\Common\Entity\Factory
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
     * @var rsanchez\Deep\Common\Property\CollectionFactoryInterface
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

    /**
     * @return AbstractEntity
     */
    abstract public function createCollection();
}
