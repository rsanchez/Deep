<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Eloquent\Builder as DeepBuilder;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\PropertyInterface;

/**
 * Abstract Hydrator class
 *
 * Hydrators bind custom fields properties to Entry objects
 */
abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $db;

    /**
     * The Entry Collection being hydrated
     * @var \rsanchez\Deep\Collection\EntryCollection
     */
    protected $collection;

    /**
     * The other hydrators
     * @var \rsanchez\Deep\Hydrator\HydratorCollection
     */
    protected $hydrators;

    /**
     * The name of the fieldtype
     * @var string
     */
    protected $fieldtype;

    /**
     * Whether to hydrate entries being populated
     * @var boolean
     */
    protected $childHydrationEnabled = true;

    /**
     * Constructor
     *
     * Set the EntryCollection and load any global elements the hydrator might need
     *
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param string                                     $fieldtype
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype)
    {
        $this->hydrators = $hydrators;
        $this->fieldtype = $fieldtype;
    }

    public function setChildHydrationDisabled()
    {
        $this->childHydrationEnabled = false;
    }

    protected function castToDeepBuilder(Builder $builder)
    {
        if (!$builder instanceof DeepBuilder) {
            $builder = new DeepBuilder($builder->getQuery(), $builder);
        }

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function bootFromCollection(EntryCollection $collection)
    {
        // load from external DBs here
    }

    /**
     * {@inheritdoc}
     */
    public function preload(EntryCollection $collection)
    {
        // load from external DBs here
    }

    /**
     * {@inheritdoc}
     */
    abstract public function hydrate(AbstractEntity $entity, PropertyInterface $property);
}
