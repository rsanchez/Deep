<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

/**
 * Global Joinable scope to automatically join the specified tables
 */
class JoinableScope implements ScopeInterface
{
    /**
     * List of Join objects created
     * @var array
     */
    protected $joins = [];

    /**
     * Join the default join tables
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function apply(Builder $builder, EloquentModel $model)
    {
        foreach ($model->defaultJoinTables() as $table) {
            $model->requireTable($builder, $table);
            $this->joins[] = end($builder->getQuery()->joins);
        }
    }

    /**
     * Unjoin the tables
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function remove(Builder $builder, EloquentModel $model)
    {
        foreach ($this->joins as $join) {
            $index = array_search($join, $builder->getQuery()->joins, true);

            if ($index !== false) {
                unset($builder->getQuery()->joins[$index]);
            }
        }
    }
}
