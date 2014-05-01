<?php

namespace rsanchez\Deep\App\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class Entries extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'deep.entry';
    }
}
