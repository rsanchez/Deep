<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\CategoryFieldCollection;

/**
 * Model for the category_fields table
 */
class CategoryField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'category_fields';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'field_id';

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'site_id' => 1,
        'field_type' => 'text',
        'field_maxl' => '128',
        'field_ta_rows' => '8',
        'field_default_fmt' => 'none',
        'field_show_fmt' => 'y',
        'field_text_direction' => 'ltr',
        'field_required' => 'n',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributeNames = [
        'site_id' => 'Site ID',
        'group_id' => 'Group ID',
        'field_name' => 'Field Name',
        'field_label' => 'Field Label',
        'field_type' => 'Field Type',
        'field_maxl' => 'Field Max Length',
        'field_ta_rows' => 'Field Textarea Rows',
        'field_default_fmt' => 'Field Default Format',
        'field_show_fmt' => 'Field Show Format',
        'field_text_direction' => 'Field Text Direction',
        'field_required' => 'Field Required',
        'field_order' => 'Field Order',
    ];

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'group_id' => 'required|exists:category_groups,group_id',
        'field_name' => 'required|alpha_dash|unique:category_fields,field_name',
        'field_label' => 'required',
        'field_type' => 'required|in:text,textarea,select',
        'field_maxl' => 'integer',
        'field_ta_rows' => 'integer',
        'field_default_fmt' => 'in:none,br,xhtml',
        'field_show_fmt' => 'yes_or_no',
        'field_text_direction' => 'in:ltr,rtl',
        'field_required' => 'yes_or_no',
        'field_order' => 'integer',
    ];

    /**
     * {@inheritdoc}
     *
     * @param  array                                             $fields
     * @return \rsanchez\Deep\Collection\CategoryFieldCollection
     */
    public function newCollection(array $fields = [])
    {
        return new CategoryFieldCollection($fields);
    }
}
