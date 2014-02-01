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
    protected $channel;
    protected $entity;
    protected $entries;

    protected $preload = false;
    protected $preloadHighPriority = false;

    public function __construct(
        $value,
        AbstractProperty $property,
        EntityCollection $entries,
        $entity = null
    ) {
        parent::__construct($value, $property);

        $this->entries = $entries;

        if ($entity instanceof Entity) {
            $this->entity = $entity;
        }

        if ($this->preload) {
            $entries->registerFieldPreloader($this->property->type(), $this, $this->preloadHighPriority);
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
