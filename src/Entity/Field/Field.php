<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Common\Field\AbstractField;
use rsanchez\Deep\Entity\Field\Factory as EntityFieldFactory;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Entity\Entity;
use rsanchez\Deep\Entity\Collection as EntityCollection;

class Field extends AbstractField
{
    protected $entity;
    protected $collection;

    protected $preload = false;
    protected $preloadHighPriority = false;

    public function __construct(
        $value,
        AbstractProperty $property,
        EntityCollection $collection,
        $entity = null
    ) {
        parent::__construct($value, $property);

        $this->collection = $collection;

        if ($entity instanceof Entity) {
            $this->entity = $entity;
        }

        if ($this->preload) {
            $collection->registerFieldPreloader($this->property->type(), $this, $this->preloadHighPriority);
        }
    }

    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function preload(DbInterface $db, array $entryIds, array $fieldIds)
    {
    }

    public function postload($payload, EntityFieldFactory $entryFieldFactory, ColFactory $colFactory)
    {
    }
}
