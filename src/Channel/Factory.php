<?php

namespace rsanchez\Deep\Channel;

use rsanchez\Deep\Channel\Field\Group as FieldGroup;
use rsanchez\Deep\Channel\Channel;
use stdClass;

class Factory
{
    public function createChannel(FieldGroup $fieldGroup, stdClass $row)
    {
        return new Channel($fieldGroup, $row);
    }
}
