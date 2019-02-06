<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Repository\RepositoryInterface;

/**
 * Relation for fetching related data from repositories instead of models
 */
class HasManyFromRepository extends Relation
{
    /**
     * @var \rsanchez\Deep\Repository\RepositoryInterface
     */
    protected $repository;

    /**
     * The repository method used when filling out the relationship
     * @var string
     */
    protected $repositoryMethod;

    protected $localKey;

    /**
     * {@inheritdoc}
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param \Illuminate\Database\Eloquent\Model           $parent
     * @param string                                        $localKey
     * @param \rsanchez\Deep\Repository\RepositoryInterface $repository
     * @param string                                        $repositoryMethod
     */
    public function __construct(Builder $query, Model $parent, $localKey, RepositoryInterface $repository, $repositoryMethod = 'find')
    {
        parent::__construct($query, $parent);

        $this->localKey = $localKey;
        $this->repository = $repository;
        $this->repositoryMethod = $repositoryMethod;
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        return $this->parent->getAttribute($this->localKey);
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraints()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addEagerConstraints(array $models)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return call_user_func([$this->repository, $this->repositoryMethod], $this->getParentKey());
    }

    /**
     * {@inheritdoc}
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $key = $model->getKey();

            if (isset($dictionary[$key])) {
                $value = $this->related->newCollection($dictionary[$key]);

                $model->setRelation($relation, $value);
            }
        }

        return $models;
    }
}
