<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Common\StorageInterface;

class Storage implements StorageInterface
{
    protected $db;

    protected $data;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke()
    {
        if (!is_null($this->data)) {
            return $this->data;
        }

        $this->data = array();

        $query = $this->db->get('fieldtypes');

        foreach ($query->result() as $row) {
            $this->data[$row->name] = $row;
        }

        $query->free_result();

        return $this->data;
    }
}
