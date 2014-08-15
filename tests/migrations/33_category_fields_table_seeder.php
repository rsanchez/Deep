<?php

use Phinx\Migration\AbstractMigration;

class CategoryFieldsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO category_fields (`field_id`, `site_id`, `group_id`, `field_name`, `field_label`, `field_type`, `field_list_items`, `field_maxl`, `field_ta_rows`, `field_default_fmt`, `field_show_fmt`, `field_text_direction`, `field_required`, `field_order`) VALUES (:field_id, :site_id, :group_id, :field_name, :field_label, :field_type, :field_list_items, :field_maxl, :field_ta_rows, :field_default_fmt, :field_show_fmt, :field_text_direction, :field_required, :field_order)');

        $query->execute([
            'field_id' => '1',
            'site_id' => '1',
            'group_id' => '1',
            'field_name' => 'cat_color',
            'field_label' => 'Color',
            'field_type' => 'select',
            'field_list_items' => 'Red
Blue
Green
Yellow',
            'field_maxl' => '128',
            'field_ta_rows' => '6',
            'field_default_fmt' => 'none',
            'field_show_fmt' => 'y',
            'field_text_direction' => 'ltr',
            'field_required' => 'y',
            'field_order' => '2',
        ]);


    }

}
