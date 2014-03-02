<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Closure;

class Builder extends EloquentBuilder
{
    /**
     * Array of relation name => collection of related models
     *
     * Cache all loaded relations here, so they can be accessed easily from the outside
     * @var array
     */
    public $relationCache = array();

    /**
     * {@inheritdoc}
     */
    protected function loadRelation(array $models, $name, Closure $constraints)
    {
       // First we will "back up" the existing where conditions on the query so we can
       // add our eager constraints. Then we will merge the wheres that were on the
       // query back to it in order that any where conditions might be specified.
       $relation = $this->getRelation($name);

       $relation->addEagerConstraints($models);

       call_user_func($constraints, $relation);

       $models = $relation->initRelation($models, $name);

       // Once we have the results, we just match those back up to their parent models
       // using the relationship instance. Then we just return the finished arrays
       // of models which have been eagerly hydrated and are readied for return.
       $results = $relation->get();

       $this->relationCache[$name] = $results;

       return $relation->match($models, $results, $name);
    }
}
