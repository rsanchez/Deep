<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Col\Col;
use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;

class Collection extends PropertyCollection
{
    protected $filterClass = __CLASS__;

    public function push(Col $col)
    {
        parent::push($col);
    }
}
