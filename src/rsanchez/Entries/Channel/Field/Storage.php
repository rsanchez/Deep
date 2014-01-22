<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\DbInterface;

class Storage
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

        $this->db->join('fieldtypes', 'fieldtypes.name = channel_fields.field_type');

        $this->db->order_by('field_order', 'ASC');

        $query = $this->db->get('channel_fields');

        foreach ($query->result() as $row) {
            if (! isset($data[$row->group_id])) {
                $this->data[$row->group_id] = array();
            }

            $this->data[$row->group_id][] = $row;
        }

        $query->free_result();

        return $this->data;
    }
}
