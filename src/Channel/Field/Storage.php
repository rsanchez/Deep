<?php

namespace rsanchez\Deep\Channel\Field;

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
        
        $this->db->order_by('field_order', 'ASC');

        $query = $this->db->get('channel_fields');

        foreach ($query->result() as $row) {
            if (! isset($this->data[$row->group_id])) {
                $this->data[$row->group_id] = array();
            }

            $this->data[$row->group_id][] = $row;
        }

        $query->free_result();

        return $this->data;
    }
}
