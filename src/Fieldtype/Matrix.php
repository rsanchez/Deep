<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entity\Collection as EntityCollection;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Channel\Field\Collection as ChannelFieldCollection;
use rsanchez\Deep\Fieldtype\Storage\Matrix as MatrixStorage;
use IteratorAggregate;
use stdClass;

class Matrix extends Fieldtype
{
    public $preload = true;
    public $preloadHighPriority = true;

    public function __construct(
        stdClass $row,
        FieldtypeRepository $fieldtypeRepository,
        ColFactory $colFactory,
        MatrixStorage $storage
    ) {
        parent::__construct($row);

        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colFactory = $colFactory;
        $this->storage = $storage;
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
        /*
        static $cols;

        foreach ($payload['cols'] as $col) {
            if (! isset($cols[$col->col_id])) {
                $cols[$col->col_id] = $this->colFactory->createProperty($col);
            }
        }
 
        foreach ($rows as &$row) {
            foreach ($payload['cols'] as $col) {
                $property = 'col_id_'.$col->col_id;
                $value = property_exists($row, $property) ? $row->$property : '';
                $field = $this->entryFieldFactory->createField($value, $cols[$col->col_id], $this->entries, $this->entity);
                $row->{$col->col_name} = $field;
            }
 
            $this->total_rows++;
 
            $this->value[] = $row;
        }
        */

        foreach ($channelFields as $channelField) {
            if (! isset($payload[$entry->entry_id][$channelField->id()])) {
                $entry->{$channelField->name()} = array();
            } else {
                $entry->{$channelField->name()} = $payload[$entry->entry_id][$channelField->id()];
            }
        }
    }
}
