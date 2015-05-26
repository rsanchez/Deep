<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\MemberFieldCollection;

/**
 * Model for the member_fields table
 */
class MemberField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'member_fields';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'm_field_id';

    /**
     * {@inheritdoc}
     *
     * @param  array                                           $fields
     * @return \rsanchez\Deep\Collection\MemberFieldCollection
     */
    public function newCollection(array $fields = [])
    {
        return new MemberFieldCollection($fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNameAttribute($value)
    {
        return $this->m_field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldIdAttribute($value)
    {
        return $this->m_field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->m_field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'm_field_id_'.$this->m_field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxLength()
    {
        return $this->m_field_maxl;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->m_field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->m_field_label;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->m_field_type;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->m_field_required === 'y';
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired($required = true)
    {
        return $this->m_field_required = $required ? 'y' : 'n';
    }

    /**
     * {@inheritdoc}
     */
    public function getListItems()
    {
        return $this->m_field_list_items ? explode("\n", $this->m_field_list_items) : [];
    }
}
