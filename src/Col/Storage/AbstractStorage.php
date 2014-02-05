<?php

namespace rsanchez\Deep\Col\Storage;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Common\StorageInterface;

abstract class AbstractStorage
{
    protected $db;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
    }

    abstract public function getByFieldIds(array $ids);

    abstract public function getByColIds(array $ids);
}
