<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Channel\Field\Group;

class GroupFactory
{
    public function createGroup($group_id)
    {
        return new Group($group_id);
    }
}
