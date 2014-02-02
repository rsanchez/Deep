<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Matrix;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Fieldtype\Storage\Matrix as MatrixStorage;
use stdClass;

class MatrixGenerator
{
    private $fieldtypeRepository;
    private $colFactory;
    private $matrixStorage;

    public function __construct(FieldtypeRepository $fieldtypeRepository, ColFactory $colFactory, MatrixStorage $matrixStorage)
    {
        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colFactory = $colFactory;
        $this->matrixStorage = $matrixStorage;
    }

    public function __invoke(stdClass $row)
    {
        return new Matrix($row, $this->fieldtypeRepository, $this->colFactory, $this->matrixStorage);
    }
}
