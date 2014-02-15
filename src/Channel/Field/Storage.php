<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Db\Db;
use rsanchez\Deep\Common\StorageInterface;

class Storage implements StorageInterface
{
    protected $db;

    protected $data;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function __invoke()
    {
        if (!is_null($this->data)) {
            return $this->data;
        }

        $this->data = array();

        $result = $this->db->table('channel_fields')
                           ->orderBy('field_order', 'asc')
                           ->get();

        foreach ($result as $row) {
            if (! isset($this->data[$row->group_id])) {
                $this->data[$row->group_id] = array();
            }

            $this->data[$row->group_id][] = $row;
        }

        return $this->data;
    }
}
