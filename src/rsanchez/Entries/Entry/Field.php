<?php

namespace rsanchez\Entries\Entry;

use rsanchez\Entries\Channel;
use rsanchez\Entries\Entry\Field\Factory as EntryFieldFactory;
use rsanchez\Entries\Channel\Field\Factory as ChannelFieldFactory;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entity;
use rsanchez\Entries\Collection as EntryCollection;

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
        Channel $channel,
        ChannelField $channelField,
        EntryCollection $entries,
        $entity = null
    ) {
        $this->channel = $channel;
        $this->channelField = $channelField;
        $this->entries = $entries;
        $this->value = $value;

        if ($entity instanceof Entity) {
            $this->entity = $entity;
        }

        if ($this->preload) {
            $entries->registerFieldPreloader($this->channelField->field_type, $this, $this->preloadHighPriority);
        }
    }

    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function __get($name)
    {
        return $this->channelField->$name;
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

    public function postload($payload, EntryFieldFactory $entryFieldFactory, ChannelFieldFactory $channelFieldFactory)
    {
    }
}
