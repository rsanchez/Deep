<?php

namespace rsanchez\Entries\Channel\Field;

use \stdClass;

class Factory
{
    private $classMap = array(
        /*
        'matrix' => 'MatrixField',
        'playa' => 'PlayaField',
        'relationships' => 'RelationshipsField',
        'grid' => 'GridField',
        */
    );

    public function createField(stdClass $row)
    {
        $class = 'rsanchez\Entries\Channel\\';

        $class .= isset($this->classMap[$row->field_type]) ? $this->classMap[$row->field_type] : 'Field';

        return new $class($row);
    }
}
