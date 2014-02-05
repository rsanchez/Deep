<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Matrix;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Col\Repository\Matrix as ColRepository;
use rsanchez\Deep\Fieldtype\Storage\Matrix as MatrixStorage;
use rsanchez\Deep\Row\Factory as RowFactory;
use rsanchez\Deep\Row\CollectionFactory as RowCollectionFactory;
use stdClass;

class MatrixGenerator
{
    private $fieldtypeRepository;
    private $colRepository;
    private $matrixStorage;

    public function __construct(
        FieldtypeRepository $fieldtypeRepository,
        ColRepository $colRepository,
        MatrixStorage $storage,
        RowFactory $rowFactory,
        RowCollectionFactory $rowCollectionFactory
    ) {
        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colRepository = $colRepository;
        $this->storage = $storage;
        $this->rowFactory = $rowFactory;
        $this->rowCollectionFactory = $rowCollectionFactory;
    }

    public function __invoke(stdClass $row)
    {
        return new Matrix(
            $row,
            $this->fieldtypeRepository,
            $this->colRepository,
            $this->storage,
            $this->rowFactory,
            $this->rowCollectionFactory
        );
    }
}
