<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Common\Field\Field;
use rsanchez\Deep\Entity\Field\Factory as EntityFieldFactory;
use rsanchez\Deep\Col\Factory as ColFactory;
use rsanchez\Deep\Entity\Collection as EntityCollection;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Entry\Entry;
use rsanchez\Deep\Entry\Entries;
use rsanchez\Deep\Fieldtype\Repository as FieldtypeRepository;
use rsanchez\Deep\Channel\Field\Collection as ChannelFieldCollection;
use IteratorAggregate;
use stdClass;

class Matrix extends Fieldtype
{
    public $preload = true;
    public $preloadHighPriority = true;

    public function __construct(
        stdClass $row,
        FieldtypeRepository $fieldtypeRepository,
        ColFactory $colFactory
        //,MatrixStorage $matrixStorage
    ) {
        parent::__construct($row);

        $this->fieldtypeRepository = $fieldtypeRepository;
        $this->colFactory = $colFactory;
        //$this->matrixStorage = $matrixStorage;
    }

    public function __invoke($value)
    {

    }

    //@TODO move this to channelField
    public function preload(Entries $entries, ChannelFieldCollection $channelFields)
    {
        $query = ee()->db->where_in('field_id', $channelFields->fieldIds())
                    ->get('matrix_cols');

        $cols = $query->result();

        $query->free_result();

        $query = ee()->db->where_in('field_id', $channelFields->fieldIds())
                    ->where_in('entry_id', $entries->entryIds())
                    ->order_by('entry_id asc, field_id asc, row_order asc')
                    ->get('matrix_data');

        $payload = array(
            'cols' => $cols,
        );

        foreach ($query->result() as $row) {
            if (! isset($payload[$row->entry_id])) {
                $payload[$row->entry_id] = array();
            }

            if (! isset($payload[$row->entry_id][$row->field_id])) {
                $payload[$row->entry_id][$row->field_id] = array();
            }

            $payload[$row->entry_id][$row->field_id][] = $row;
        }

        $query->free_result();

        return $payload;
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
