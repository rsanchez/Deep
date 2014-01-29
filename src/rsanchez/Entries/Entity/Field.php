<?php

namespace rsanchez\Entries\Entity;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entity\Field\Factory as EntityFieldFactory;
use rsanchez\Entries\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entity;
use rsanchez\Entries\EntityCollection;

class Field
{
    protected $channel;
    protected $channelField;
    protected $entity;
    protected $entries;
    public $value;

    protected $preload = false;
    protected $preloadHighPriority = false;

    public function __construct(
        $value,
        ChannelField $channelField,
        EntityCollection $entries,
        $entity = null
    ) {
        $this->channelField = $channelField;
        $this->entries = $entries;
        $this->value = $value;

        if ($entity instanceof Entity) {
            $this->entity = $entity;
        }

        if ($this->preload) {
            $entries->registerFieldPreloader($this->channelField->type(), $this, $this->preloadHighPriority);
        }
    }

    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function settings()
    {
        return $this->channelField->settings();
    }

    public function id()
    {
        return $this->channelField->id();
    }

    public function type()
    {
        return $this->channelField->type();
    }

    public function name()
    {
        return $this->channelField->name();
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function __invoke()
    {
        return $this->__toString();
    }

    public function preload(DbInterface $db, array $entryIds, array $fieldIds)
    {
    }

    public function postload($payload, EntityFieldFactory $entryFieldFactory, ChannelFieldFactory $channelFieldFactory)
    {
    }
}
