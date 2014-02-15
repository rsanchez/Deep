<?php

namespace rsanchez\Deep\Col\Storage;

use rsanchez\Deep\Col\Storage\AbstractStorage;

class Matrix extends AbstractStorage
{
    public function getByFieldIds(array $ids)
    {
        return $this->db->table('matrix_cols')
                           ->whereIn('field_id', $ids)
                           ->get();
    }

    public function getByColIds(array $ids)
    {
        return $this->db->table('matrix_cols')
                           ->whereIn('col_id', $ids)
                           ->get();
    }
}
