<?php

namespace rsanchez\Deep\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends BaseBuilder
{
    /**
     * Whether or not to hydrate custom fields
     * @var bool
     */
    protected $hydrationEnabled = true;

    /**
     * Whether or not to hydrate children's custom fields
     * @var bool
     */
    protected $childHydrationEnabled = true;

    /**
     * Which fields to hydrate
     * @var bool
     */
    protected $withFields = [];

    public function __construct(QueryBuilder $query, BaseBuilder $builder)
    {
        parent::__construct($query);
        $this->model = $builder->model;
        $this->eagerLoad = $builder->eagerLoad;
        $macrosProp = isset($builder->localMacros) ? 'localMacros' : 'macros';
        $this->{$macrosProp} = $builder->{$macrosProp};
        $this->onDelete = $builder->onDelete;
        $this->passthru = $builder->passthru;

        if (isset($builder->scopes)) {
            $this->scopes = $builder->scopes;
        }

        if (isset($builder->removedScopes)) {
            $this->removedScopes = $builder->removedScopes;
        }
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($columns = ['*'])
    {
        $builder = method_exists($this, 'applyScopes') ? $this->applyScopes() : $this;

        $models = $builder->getModels($columns);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $builder->getModel()->newCollection($models, $builder);
    }

    public function setWithFields($fieldNames)
    {
        $this->withFields = $fieldNames;

        return $this;
    }

    public function setHydrationEnabled()
    {
        $this->hydrationEnabled = true;

        return $this;
    }

    public function setHydrationDisabled()
    {
        $this->hydrationEnabled = false;

        return $this;
    }

    public function setChildHydrationEnabled()
    {
        $this->childHydrationEnabled = true;

        return $this;
    }

    public function setChildHydrationDisabled()
    {
        $this->childHydrationEnabled = false;

        return $this;
    }

    public function getWithFields()
    {
        return $this->withFields;
    }

    public function isHydrationEnabled()
    {
        return $this->hydrationEnabled;
    }

    public function isChildHydrationEnabled()
    {
        return $this->childHydrationEnabled;
    }
}
