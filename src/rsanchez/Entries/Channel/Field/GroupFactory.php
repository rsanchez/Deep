<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Db\DbInterface;
use rsanchez\Entries\Channel\Field\Group;

class GroupFactory
{
    public function createGroup($group_id)
    {
        return new Group($group_id);
    }
}
