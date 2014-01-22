<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\Channel;
use \stdClass;

class Factory
{
    public function __invoke(FieldGroups $fieldGroups, FieldGroupFactory $fieldGroupFactory, stdClass $row)
    {
        $fieldGroup = $fieldGroups->find($row->field_group);

        return new Channel($fieldGroup, $row);
    }
}
