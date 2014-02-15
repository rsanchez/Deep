<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Col\Col;
use rsanchez\Deep\Common\Property\AbstractCollection as PropertyCollection;

class Collection extends PropertyCollection
{
    protected $filterClass = __CLASS__;

    public function attach(Col $col)
    {
        parent::attach($col);
    }
}
