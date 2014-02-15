<?php

namespace rsanchez\Deep\Channel;

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

        return $this->db->table('channels')->get();
    }
}
