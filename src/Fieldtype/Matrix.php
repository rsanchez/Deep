<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Col\Repository\Matrix as ColRepository;
use rsanchez\Deep\Row\Factory as RowFactory;
use rsanchez\Deep\Row\CollectionFactory as RowCollectionFactory;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Channel\Field\Collection as ChannelFieldCollection;
use rsanchez\Deep\Fieldtype\Storage\Matrix as MatrixStorage;
use stdClass;

class Matrix extends Fieldtype
{
    public $preload = true;
    public $preloadHighPriority = true;

    public function __construct(
        stdClass $row,
        FieldtypeRepository $fieldtypeRepository,
        ColRepository $colRepository,
        MatrixStorage $storage,
        RowFactory $rowFactory,
        RowCollectionFactory $rowCollectionFactory
    ) {
        parent::__construct($row);

        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colRepository = $colRepository;
        $this->storage = $storage;
        $this->rowFactory = $rowFactory;
        $this->rowCollectionFactory = $rowCollectionFactory;
    }

    public function __invoke($value)
    {

    }

    //@TODO move this to channelField
    public function preload(Entries $entries, ChannelFieldCollection $channelFields)
    {
        return call_user_func($this->storage, $entries->entryIds(), $channelFields->fieldIds());
    }

    public function hydrate(Entry $entry, ChannelFieldCollection $channelFields, $payload)
    {
        $this->colRepository->findByFieldIds($channelFields->fieldIds());

        foreach ($channelFields as $channelField) {
            if (! isset($payload[$entry->entry_id][$channelField->id()])) {
                $entry->{$channelField->name()} = array();
            } else {
                $rowCollection = $this->rowCollectionFactory->createCollection();

                $cols = $this->colRepository->filterByFieldId($channelField->id());

                foreach ($payload[$entry->entry_id][$channelField->id()] as $row) {
                    $row = $this->rowFactory->createRow($row, $cols);

                    $rowCollection->attach($row);
                }

                $entry->{$channelField->name()} = $rowCollection;
            }
        }
    }
}
