<?php

namespace rsanchez\Entries\Entity\Field;

use rsanchez\Entries\FilePath\FilePaths;
use rsanchez\Entries\Channel\Channel;
use rsanchez\Entries\Entity\Field;
use rsanchez\Entries\Entries;
use rsanchez\Entries\Property;
use rsanchez\Entries\Col\Factory as ColFactory;
use rsanchez\Entries\Entity\Entity;
use \Pimple;

class Factory extends Pimple
{
    public function __construct(FilePaths $filePaths, ColFactory $colFactory)
    {
        parent::__construct();

        $this['filePaths'] = $filePaths;
        $this['colFactory'] = $colFactory;

        $this['date'] = $this->factory(function ($container) {
            return new Date($container['value'], $container['property'], $container['entries'], $container['entry']);
        });

        $this['file'] = $this->factory(function ($container) {
            return new File($container['value'], $container['property'], $container['entries'], $container['entry'], $container['filePaths']);
        });

        $this['matrix'] = $this->factory(function ($container) {
            return new Matrix($container['value'], $container['property'], $container['entries'], $container['entry'], $container, $container['colFactory']);
        });

        $this['field'] = $this->factory(function ($container) {
            return new Field($container['value'], $container['property'], $container['entries'], $container['entry']);
        });
    }

    public function createField(
        $value,
        Property $property,
        Entries $entries,
        $entry = null
    ) {
        $this['property'] = $property;
        $this['entries'] = $entries;
        $this['entry'] = $entry;
        $this['value'] = $value;

        if (isset($this[$property->type()])) {
            return $this[$property->type()];
        }

        return $this['field'];
    }
}
