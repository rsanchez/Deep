<?php

namespace rsanchez\Deep\Model\Collection;

use Illuminate\Database\Eloquent\Collection;

class AssetsCollection extends Collection
{
    public function __toString()
    {
        $asset = $this->first();

        return $asset ? $asset->url : '';
    }
}
