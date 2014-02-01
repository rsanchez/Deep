<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Property\AbstractProperty;
use stdClass;

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

    public function __construct(stdClass $row, Fieldtype $fieldtype)
    {
        parent::__construct($row, $fieldtype);

        if ($this->field_settings) {
            $this->field_settings = unserialize(base64_decode($this->field_settings));
        } else {
            $this->field_settings = array();
        }

        if ($this->fieldtype->has_global_settings === 'y') {
            $this->field_settings = array_merge($this->fieldtype->settings, $this->field_settings);
        }
    }

    public function prefix()
    {
        return 'field_id_';
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
