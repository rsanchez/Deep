<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Property\AbstractProperty;
use stdClass;

class Col extends AbstractProperty
{
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

    public function __construct(stdClass $row, Fieldtype $fieldtype)
    {
        parent::__construct($row, $fieldtype);

        if ($this->col_settings) {
            $this->col_settings = unserialize(base64_decode($this->col_settings));
        } else {
            $this->col_settings = array();
        }

        if ($this->has_global_settings === 'y') {
            $this->col_settings = array_merge($this->fieldtype->settings, $this->col_settings);
        }
    }

    public function inputName()
    {
        return $this->prefix() . $this->id();
    }

    public function prefix()
    {
        return 'field_id_';
    }

    public function settings()
    {
        return $this->col_settings;
    }

    public function id()
    {
        return $this->col_id;
    }

    public function type()
    {
        return $this->col_type;
    }

    public function name()
    {
        return $this->col_name;
    }
}
