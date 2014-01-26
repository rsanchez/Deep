<?php

namespace rsanchez\Entries\Channel;

use \rsanchez\Entries\Channel;

class Field
{
    // exp_fieldtypes
    public $fieldtype_id;
    public $name;
    public $version;
    public $settings;
    public $has_global_settings;

    // exp_channel_fields
    public $field_id;
    public $site_id;
    public $group_id;
    public $field_name;
    public $field_label;
    public $field_instructions;
    public $field_type;
    public $field_list_items;
    public $field_pre_populate;
    public $field_pre_channel_id;
    public $field_pre_field_id;
    public $field_ta_rows;
    public $field_maxl;
    public $field_required;
    public $field_text_direction;
    public $field_search;
    public $field_is_hidden;
    public $field_fmt;
    public $field_show_fmt;
    public $field_order;
    public $field_content_type;
    public $field_settings;

    // exp_matrix_cols
    public $col_id;
    public $col_name;
    public $col_label;
    public $col_instructions;
    public $col_type;
    public $col_required;
    public $col_search;
    public $col_order;
    public $col_width;
    public $col_settings;

    public function __construct(\stdClass $result)
    {
        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $value) {
            if (property_exists($result, $property)) {
                $this->$property = $result->$property;
            }
        }

        if ($this->field_settings) {
            $this->field_settings = unserialize(base64_decode($this->field_settings));
        } else {
            $this->field_settings = array();
        }

        if ($this->has_global_settings === 'y') {
            $this->field_settings = array_merge(unserialize(base64_decode($this->settings)), $this->field_settings);
        }

        if ($this->col_settings) {
            $this->col_settings = unserialize(base64_decode($this->col_settings));
        } else {
            $this->col_settings = array();
        }
    }
}
