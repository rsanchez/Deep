<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\CategoryCollection;

/**
 * Model for the categories table
 */
class Category extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'cat_id';

    /**
     * {@inheritdoc}
     *
     * @param  array $models
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function newCollection(array $models = array())
    {
        return new CategoryCollection($models);
    }

    /**
     * {@inheritdoc}
     */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted);

        $query->select($this->table.'.*');

        return $query;
    }

    /**
     * Order by Category Nesting
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNested(Builder $query)
    {
        $connection = $query->getQuery()->getConnection();

        $tablePrefix = $connection->getTablePrefix();

        return $query->leftJoin("{$this->table} AS subcategories", $connection->raw('`subcategories`.`cat_id`'), '=', 'categories.parent_id')
            ->orderBy($connection->raw("coalesce(`subcategories`.`cat_url_title`, `{$tablePrefix}categories`.`cat_url_title`), `{$tablePrefix}categories`.`cat_order`"));
    }
}
