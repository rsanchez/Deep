<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Col\Collection as ColCollection;
use rsanchez\Deep\Entity\Factory as EntityFactory;
use rsanchez\Deep\Row\Row;
use stdClass;

class Factory extends EntityFactory
{
    public function createEntity(stdClass $row, ColCollection $colCollection)
    {
        return new Row($row, $colCollection);
    }

    public function createRow(stdClass $row, ColCollection $colCollection)
    {
        return $this->createEntity($row, $colCollection);
    }
}
