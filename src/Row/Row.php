<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Entity\Entity;

class Row extends Entity
{
    public $row_id;
    public $site_id;
    public $entry_id;
    public $field_id;
    public $var_id;
    public $row_order;

    public function id()
    {
        return $this->row_id;
    }
}
