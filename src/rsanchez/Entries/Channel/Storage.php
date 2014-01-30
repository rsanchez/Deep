<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\Db\DbInterface;
use rsanchez\Entries\StorageInterface;

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

        $query = $this->db->get('exp_channels');

        $this->data = $query->result();

        $query->free_result();

        return $this->data;
    }
}
