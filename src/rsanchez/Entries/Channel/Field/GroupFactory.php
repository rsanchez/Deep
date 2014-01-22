<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\DbInterface;
use rsanchez\Entries\Channel\Field\Group;

class GroupFactory
{
    public function __invoke()
    {
        return new Group();
    }
}
