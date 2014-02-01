<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Property\AbstractFactory;
use rsanchez\Deep\Col\Col;
use stdClass;

class Factory extends AbstractFactory
{
    /**
     * @inheritdoc
     * @return rsanchez\Deep\Col\Col
     */
    public function createProperty(stdClass $row)
    {
        return new Col($row, $this->fieldtypeRepository->find($row->col_type));
    }
}
