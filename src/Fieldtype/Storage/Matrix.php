<?php

namespace rsanchez\Deep\Fieldtype\Storage;

use rsanchez\Deep\Db\Db;

class Matrix
{
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function __invoke(array $entryIds, array $fieldIds)
    {
        $result = $this->db->table('matrix_data')
                    ->whereIn('field_id', $fieldIds)
                    ->whereIn('entry_id', $entryIds)
                    ->orderBy('entry_id', 'asc')
                    ->orderBy('field_id', 'asc')
                    ->orderBy('row_order', 'asc')
                    ->get();

        foreach ($result as $row) {
            if (! isset($payload[$row->entry_id])) {
                $payload[$row->entry_id] = array();
            }

            if (! isset($payload[$row->entry_id][$row->field_id])) {
                $payload[$row->entry_id][$row->field_id] = array();
            }

            $payload[$row->entry_id][$row->field_id][] = $row;
        }

        return $payload;
    }
}
