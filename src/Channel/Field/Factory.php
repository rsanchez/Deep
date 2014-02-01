<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Property\AbstractFactory as AbstractPropertyFactory;
use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use stdClass;

class Factory extends AbstractPropertyFactory
{
    /**
     * @inheritdoc
     * @return rsanchez\Deep\Channel\Field
     */
    public function createProperty(stdClass $row)
    {
        return new Field($row, $this->fieldtypeRepository->find($row->field_type));
    }
}
