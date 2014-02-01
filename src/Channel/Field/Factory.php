<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Property\FactoryInterface as PropertyFactoryInterface;
use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use stdClass;

class Factory implements PropertyFactoryInterface
{
    /**
     * @var rsanchez\Deep\Fieldtype\Repository
     */
    private $fieldtypeRepository;

    public function __construct(FieldtypeRepository $fieldtypeRepository)
    {
        $this->fieldtypeRepository = $fieldtypeRepository;
    }

    /**
     * @inheritdoc
     * @return rsanchez\Deep\Channel\Field
     */
    public function createProperty(stdClass $row)
    {
        return new Field($row, $this->fieldtypeRepository->find($row->field_type));
    }
}
