<?php

namespace rsanchez\Deep\Col\Storage;

use rsanchez\Deep\Db\Db;

abstract class AbstractStorage
{
    protected $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    abstract public function getByFieldIds(array $ids);

    abstract public function getByColIds(array $ids);
}
