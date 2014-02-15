<?php

namespace rsanchez\Deep\Common\Property;

use rsanchez\Deep\Common\Property\AbstractProperty;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use stdClass;

abstract class AbstractFactory
{
    /**
     * @var rsanchez\Deep\Fieldtype\Repository
     */
    protected $fieldtypeRepository;

    public function __construct(FieldtypeRepository $fieldtypeRepository)
    {
        $this->fieldtypeRepository = $fieldtypeRepository;
    }

    /**
     * Create a class that inherits AbstractProperty
     * @param  stdClass                                $row
     * @return rsanchez\Deep\Common\Property\AbstractProperty
     */
    abstract public function createProperty(stdClass $row);
}
