<?php

namespace rsanchez\Deep\Fieldtype\Storage;

use rsanchez\Deep\Db\DbInterface;

class Matrix
{
    public function __construct(DbInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(array $entryIds, array $fieldIds)
    {
        $query = ee()->db->where_in('field_id', $fieldIds)
                    ->get('matrix_cols');

        $cols = $query->result();

        $query->free_result();

        $query = ee()->db->where_in('field_id', $fieldIds)
                    ->where_in('entry_id', $entryIds)
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
}
