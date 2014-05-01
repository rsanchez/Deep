<?php

namespace rsanchez\Deep\App\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class Categories extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'deep.category';
    }
}
