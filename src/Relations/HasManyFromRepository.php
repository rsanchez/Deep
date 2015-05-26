<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Repository\RepositoryInterface;

/**
 * Relation for fetching related data from repositories instead of models
 */
class HasManyFromRepository extends HasMany
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

    /**
     * {@inheritdoc}
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param \Illuminate\Database\Eloquent\Model           $parent
     * @param string                                        $foreignKey
     * @param string                                        $localKey
     * @param \rsanchez\Deep\Repository\RepositoryInterface $repository
     * @param string                                        $repositoryMethod
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, RepositoryInterface $repository, $repositoryMethod = 'find')
    {
        parent::__construct($query, $parent, $foreignKey, $localKey);

        $this->repository = $repository;
        $this->repositoryMethod = $repositoryMethod;
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
}
