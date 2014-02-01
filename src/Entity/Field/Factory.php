<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Entity\Field\Field;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entity\Entity;
use rsanchez\Deep\Common\Field\AbstractFactory;

class Factory extends AbstractFactory
{
    public function createField(
        $value,
        AbstractProperty $property,
        Entries $entries,
        $entry = null
    ) {
        return new Field($value, $property, $entries, $entry);
    }
}
