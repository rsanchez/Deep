<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Entity\Entity;
use rsanchez\Deep\Entity\Factory as EntityFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Property\CollectionFactoryInterface as PropertyCollectionFactory;
use Iterator;

class Collection implements Iterator
{
    public $total_results = 0;
    public $count = 1;

    protected $entityIds = array();

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

    protected $entities = array();

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
     * Fill this collection with entries created from a DB result set
     * @param stdClass[] $result 
     * @return void
     */
    public function fill(array $result)
    {
    }

    public function attach(Entity $entity)
    {
        array_push($this->entities, $entity);
        $this->total_results++;
    }

    public function rewind()
    {
        $this->count = 1;
    }

    public function current()
    {
        return $this->entities[$this->count - 1];
    }

    public function key()
    {
        return $this->count - 1;
    }

    public function next()
    {
        ++$this->count;
    }

    public function valid()
    {
        return array_key_exists($this->count - 1, $this->entities);
    }
}
