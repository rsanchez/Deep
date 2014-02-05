<?php

namespace rsanchez\Deep\Col\Storage;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Col\Storage\AbstractStorage;

class Matrix extends AbstractStorage
{
    public function getByFieldIds(array $ids)
    {
        $this->db->where_in('field_id', $ids);

        $query = $this->db->get('exp_matrix_cols');

        $result = $query->result();

        $query->free_result();

        return $result;
    }

    public function getByColIds(array $ids)
    {
        $this->db->where_in('col_id', $ids);

        $query = $this->db->get('exp_matrix_cols');

        $result = $query->result();

        $query->free_result();

        return $result;
    }
}
