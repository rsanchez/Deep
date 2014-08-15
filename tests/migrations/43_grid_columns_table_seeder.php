<?php

use Phinx\Migration\AbstractMigration;

class GridColumnsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO grid_columns (`col_id`, `field_id`, `content_type`, `col_order`, `col_type`, `col_label`, `col_name`, `col_instructions`, `col_required`, `col_search`, `col_width`, `col_settings`) VALUES (:col_id, :field_id, :content_type, :col_order, :col_type, :col_label, :col_name, :col_instructions, :col_required, :col_search, :col_width, :col_settings)');

        $query->execute([
            'col_id' => '1',
            'field_id' => '12',
            'content_type' => 'channel',
            'col_order' => '0',
            'col_type' => 'text',
            'col_label' => 'Text',
            'col_name' => 'text',
            'col_instructions' => '',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_width' => '0',
            'col_settings' => '{"field_fmt":"none","field_content_type":"all","field_text_direction":"ltr","field_maxl":"256","field_required":"n"}',
        ]);

        $query->execute([
            'col_id' => '2',
            'field_id' => '12',
            'content_type' => 'channel',
            'col_order' => '1',
            'col_type' => 'file',
            'col_label' => 'File',
            'col_name' => 'file',
            'col_instructions' => '',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_width' => '0',
            'col_settings' => '{"field_content_type":"all","allowed_directories":"all","show_existing":"y","num_existing":"50","field_required":"n"}',
        ]);

        $query->execute([
            'col_id' => '3',
            'field_id' => '12',
            'content_type' => 'channel',
            'col_order' => '2',
            'col_type' => 'relationship',
            'col_label' => 'Relationships',
            'col_name' => 'relationships',
            'col_instructions' => '',
            'col_required' => 'n',
            'col_search' => 'n',
            'col_width' => '0',
            'col_settings' => '{"channels":[],"expired":0,"future":0,"categories":[],"authors":[],"statuses":[],"limit":"100","order_field":"title","order_dir":"asc","allow_multiple":"1","field_required":"n"}',
        ]);


    }

}
