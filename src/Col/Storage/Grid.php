<?php

namespace rsanchez\Deep\Col\Storage;

use rsanchez\Deep\Col\Storage\AbstractStorage;

class Grid extends AbstractStorage
{
    public function getByFieldIds(array $ids)
    {
        return $this->db->table('grid_columns')
                            ->whereIn('field_id', $ids)
                            ->get();
    }

    public function getByColIds(array $ids)
    {
        return $this->db->table('grid_columns')
                           ->whereIn('col_id', $ids)
                           ->get();
    }
}
