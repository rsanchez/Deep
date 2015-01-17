<?php

namespace rsanchez\Deep\Validation;

use Illuminate\Validation\DatabasePresenceVerifier as IlluminateDatabasePresenceVerifier;

class DatabasePresenceVerifier extends IlluminateDatabasePresenceVerifier
{
    /**
     * {@inheritdoc}
     */
    protected function addWhere($query, $key, $extraValue)
    {
        if (is_array($extraValue)) {
            $query->whereIn($key, $extraValue);
        } else {
            parent::addWhere($query, $key, $extraValue);
        }
    }
}
