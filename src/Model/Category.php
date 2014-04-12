<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Model\AbstractJoinableModel;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\CategoryCollection;
use rsanchez\Deep\Repository\CategoryFieldRepository;

/**
 * Model for the categories table
 */
class Category extends AbstractJoinableModel 
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
     * Global Category Field Repository
     * @var \rsanchez\Deep\Repository\CategoryFieldRepository
     */
    protected static $categoryFieldRepository;

    /**
     * Set the global CategoryFieldRepository
     * @param  \rsanchez\Deep\Repository\CategoryFieldRepository $categoryFieldRepository
     * @return void
     */
    public static function setCategoryFieldRepository(CategoryFieldRepository $categoryFieldRepository)
    {
        self::$categoryFieldRepository = $categoryFieldRepository;
    }

    /**
     * Join with category_data
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query;
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFields(Builder $query)
    {
        return $this->requireTable($query, 'category_field_data')
            ->addSelect('categories.*')
            ->addSelect('category_field_data.*');
    }

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        return array(
            'category_field_data' => function ($query) {
                $query->join('category_field_data', 'category_field_data.cat_id', '=', 'categories.cat_id');
            },
        );
    }

    /**
     * Alias custom field names
     *
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        if (! isset($this->attributes[$name]) && self::$categoryFieldRepository->hasField($name)) {
            $name = 'field_id_'.self::$categoryFieldRepository->getFieldId($name);
        }

        return parent::getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function attributesToArray()
    {
        $array = parent::attributesToArray();

        foreach ($array as $key => $value) {
            if (strncmp($key, 'field_id_', 9) === 0) {
                $id = substr($key, 9);

                if (self::$categoryFieldRepository->hasFieldId($id)) {
                    $array[self::$categoryFieldRepository->getFieldName($id)] = $value;
                }

                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                        $models
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
