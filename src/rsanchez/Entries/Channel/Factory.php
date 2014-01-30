<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\Channel\Field\Group as FieldGroup;
use rsanchez\Entries\Channel\Channel;
use \stdClass;

class Factory
{
    public function createChannel(FieldGroup $fieldGroup, stdClass $row)
    {
        return new Channel($fieldGroup, $row);
    }
}
