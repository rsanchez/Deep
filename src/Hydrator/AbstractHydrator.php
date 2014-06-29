<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\AbstractProperty;

/**
 * Abstract Hydrator class
 *
 * Hydrators bind custom fields properties to Entry objects
 */
abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * The Entry Collection being hydrated
     * @var \rsanchez\Deep\Collection\EntryCollection
     */
    protected $collection;

    /**
     * The name of the fieldtype
     * @var string
     */
    protected $fieldtype;

    /**
     * Constructor
     *
     * Set the EntryCollection and load any global elements the hydrator might need
     * @param \rsanchez\Deep\Collection\EntryCollection $collection
     * @param string                                    $fieldtype
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        $this->collection = $collection;
        $this->fieldtype = $fieldtype;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        // load from external DBs here
    }

    /**
     * {@inheritdoc}
     */
    abstract public function hydrate(AbstractEntity $entity, AbstractProperty $property);
}
