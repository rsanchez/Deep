<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Common\Entity\AbstractCollectionFactory as EntityCollectionFactory;
use rsanchez\Deep\Row\Collection;
use rsanchez\Deep\Row\Factory as RowFactory;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Fieldtype\CollectionFactory as FieldtypeCollectionFactory;
use rsanchez\Deep\Col\CollectionFactory as ColCollectionFactory;

class CollectionFactory extends EntityCollectionFactory
{
    /**
     * @var rsanchez\Deep\Row\Factory
     */
    protected $factory;

    /**
     * @var rsanchez\Deep\Col\CollectionFactory
     */
    protected $colCollectionFactory;

    public function __construct(
        RowFactory $factory,
        FieldtypeRepository $fieldtypeRepository,
        FieldtypeCollectionFactory $fieldtypeCollectionFactory,
        ColCollectionFactory $colCollectionFactory
    ) {
        parent::__construct(
            $factory,
            $fieldtypeRepository,
            $fieldtypeCollectionFactory,
            $colCollectionFactory
        );

        $this->colCollectionFactory = $colCollectionFactory;
    }

    public function createCollection()
    {
        return new Collection(
            $this->factory,
            $this->fieldtypeRepository,
            $this->fieldtypeCollectionFactory,
            $this->colCollectionFactory
        );
    }
}
