<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use rsanchez\Entries\Channel\Field\Collection;
use IteratorAggregate;
use ArrayIterator;

class Group extends Collection
{
    public $group_id;

    public function __construct($group_id)
    {
        $this->group_id = $group_id;
    }
}
