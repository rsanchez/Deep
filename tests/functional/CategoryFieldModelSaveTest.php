<?php

use rsanchez\Deep\Model\CategoryField;

class CategoryFieldModelSaveTest extends AbstractModelSaveTest
{
    protected function getModelAttributes()
    {
        return [
            'site_id' => '1',
            'group_id' => '1',
            'field_name' => 'cat_field',
            'field_label' => 'Cat Field',
            'field_type' => 'text',
            'field_list_items' => "A\nB",
            'field_maxl' => '255',
            'field_ta_rows' => '6',
            'field_default_fmt' => 'none',
            'field_show_fmt' => 'n',
            'field_text_direction' => 'ltr',
            'field_required' => 'n',
            'field_order' => '',
        ];
    }

    public function testSiteIdRequiredValidation()
    {
        $this->validateExceptionTest(['site_id' => ''], 'The Site ID field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected Site ID is invalid.');
    }

    public function testGroupIdRequiredValidation()
    {
        $this->validateExceptionTest(['group_id' => ''], 'The Group ID field is required.');
    }

    public function testGroupIdExistsValidation()
    {
        $this->validateExceptionTest(['group_id' => '10'], 'The selected Group ID is invalid.');
    }

    public function testFieldNameRequiredValidation()
    {
        $this->validateExceptionTest(['field_name' => ''], 'The Field Name field is required.');
    }

    public function testFieldNameAlphaDashValidation()
    {
        $this->validateExceptionTest(['field_name' => 'Foo bar'], 'The Field Name may only contain letters, numbers, and dashes.');
    }

    public function testFieldNameUniqueValidation()
    {
        $this->validateExceptionTest(['field_name' => 'cat_color'], 'The Field Name has already been taken.');
    }

    public function testFieldLabelRequiredValidation()
    {
        $this->validateExceptionTest(['field_label' => ''], 'The Field Label field is required.');
    }

    public function testFieldTypeRequiredValidation()
    {
        $this->validateExceptionTest(['field_type' => ''], 'The Field Type field is required.');
    }

    public function testFieldTypeInValidation()
    {
        $this->validateExceptionTest(['field_type' => 'foo'], 'The selected Field Type is invalid.');
    }

    public function testFieldMaxlIntegerValidation()
    {
        $this->validateExceptionTest(['field_maxl' => 'foo'], 'The Field Max Length must be an integer.');
    }

    public function testFieldTaRowsIntegerValidation()
    {
        $this->validateExceptionTest(['field_ta_rows' => 'foo'], 'The Field Textarea Rows must be an integer.');
    }

    public function testFieldDefaultFmtInValidation()
    {
        $this->validateExceptionTest(['field_default_fmt' => 'foo'], 'The selected Field Default Format is invalid.');
    }

    public function testFieldShowFmtYesOrNoValidation()
    {
        $this->validateExceptionTest(['field_show_fmt' => 'foo'], 'The Field Show Format field must be y or n.');
    }

    public function testFieldTextDirectionInValidation()
    {
        $this->validateExceptionTest(['field_text_direction' => 'foo'], 'The selected Field Text Direction is invalid.');
    }

    public function testFieldRequiredYesOrNoValidation()
    {
        $this->validateExceptionTest(['field_show_fmt' => 'foo'], 'The Field Show Format field must be y or n.');
    }

    public function testFieldOrderIntegerValidation()
    {
        $this->validateExceptionTest(['field_order' => 'integer'], 'The Field Order must be an integer.');
    }
}
