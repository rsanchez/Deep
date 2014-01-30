<?php

namespace rsanchez\Deep\Channel;

use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Property\AbstractProperty;

class Field extends AbstractProperty
{
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

    public function __construct(\stdClass $row)
    {
        parent::__construct($row);

        if ($this->field_settings) {
            $this->field_settings = unserialize(base64_decode($this->field_settings));
        } else {
            $this->field_settings = array();
        }

        if ($this->has_global_settings === 'y') {
            $this->field_settings = array_merge(unserialize(base64_decode($this->settings)), $this->field_settings);
        }

        /*
        if ($this->col_settings) {
            $this->col_settings = unserialize(base64_decode($this->col_settings));
        } else {
            $this->col_settings = array();
        }
        */
    }

    public function settings()
    {
        return $this->field_settings;
    }

    public function id()
    {
        return $this->field_id;
    }

    public function type()
    {
        return $this->field_type;
    }

    public function name()
    {
        return $this->field_name;
    }
}
