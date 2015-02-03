<?php

namespace rsanchez\Deep\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GridRowHasMany extends HasMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelationCountQuery(Builder $query, Builder $parent)
    {
        $query->select(new Expression('count(*)'));

        return $query->where($this->getHasCompareKey(), '=', $this->getParentKey());
    }
}
