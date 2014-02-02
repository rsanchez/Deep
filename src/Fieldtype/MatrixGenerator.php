<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Matrix;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Col\Factory as ColFactory;
use stdClass;

class MatrixGenerator
{
    private $fieldtypeRepository;
    private $colFactory;

    public function __construct(FieldtypeRepository $fieldtypeRepository, ColFactory $colFactory)
    {
        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colFactory = $colFactory;
    }

    public function __invoke(stdClass $row)
    {
        return new Matrix($row, $this->fieldtypeRepository, $this->colFactory);
    }
}
