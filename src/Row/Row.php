<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Entity\Entity;

class Row extends Entity
{
    public function id()
    {
        return $this->row_id;
    }
}
