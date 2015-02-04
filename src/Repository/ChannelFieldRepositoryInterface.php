<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\ChannelCollection;

/**
 * Repository of all Fields
 */
interface ChannelFieldRepositoryInterface
{
    /**
     * Get the collection of Fields
     *
     * @return \rsanchez\Deep\Collection\AbstractFieldCollection
     */
    public function getFields();

    /**
     * Get the field_id for the specified field name
     *
     * @param  string                     $field name of the field
     * @return \rsanchez\Deep\Model\Field
     */
    public function getFieldId($field);

    /**
     * Get the field_id for the specified field name
     *
     * @param  int    $id id of the field
     * @return string
     */
    public function getFieldName($id);

    /**
     * Check if this collection has the specified field name
     *
     * @param  string  $field the name of the field
     * @return boolean
     */
    public function hasField($field);

    /**
     * Check if this collection has the specified field id
     *
     * @param  int     $id the id of the field
     * @return boolean
     */
    public function hasFieldId($id);

    /**
     * Find an Field by ID
     * @var int $id
     * @return \rsanchez\Deep\Model\AbstractField|null
     */
    public function find($id);

    /**
     * Get the Collection of all items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCollection();

    /**
     * Get the Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();

    /**
     * Get a Collection of fields from the specified group
     *
     * @param  int                                       $groupId
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByGroup($groupId);

    /**
     * Get the fields used by the channels in the specified collection
     * @param  \rsanchez\Deep\Collection\ChannelCollection $channels
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByChannelCollection(ChannelCollection $channels);
}
