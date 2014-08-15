<?php

use Phinx\Migration\AbstractMigration;

class AssetsSelectionsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO assets_selections (`file_id`, `entry_id`, `field_id`, `col_id`, `row_id`, `var_id`, `element_id`, `content_type`, `sort_order`, `is_draft`) VALUES (:file_id, :entry_id, :field_id, :col_id, :row_id, :var_id, :element_id, :content_type, :sort_order, :is_draft)');

        $query->execute([
            'file_id' => '6',
            'entry_id' => '8',
            'field_id' => '1',
            'col_id' => null,
            'row_id' => null,
            'var_id' => null,
            'element_id' => null,
            'content_type' => null,
            'sort_order' => '2',
            'is_draft' => '0',
        ]);

        $query->execute([
            'file_id' => '2',
            'entry_id' => '7',
            'field_id' => '1',
            'col_id' => null,
            'row_id' => null,
            'var_id' => null,
            'element_id' => null,
            'content_type' => null,
            'sort_order' => '1',
            'is_draft' => '0',
        ]);

        $query->execute([
            'file_id' => '1',
            'entry_id' => '7',
            'field_id' => '1',
            'col_id' => null,
            'row_id' => null,
            'var_id' => null,
            'element_id' => null,
            'content_type' => null,
            'sort_order' => '0',
            'is_draft' => '0',
        ]);

        $query->execute([
            'file_id' => '5',
            'entry_id' => '8',
            'field_id' => '1',
            'col_id' => null,
            'row_id' => null,
            'var_id' => null,
            'element_id' => null,
            'content_type' => null,
            'sort_order' => '1',
            'is_draft' => '0',
        ]);

        $query->execute([
            'file_id' => '4',
            'entry_id' => '8',
            'field_id' => '1',
            'col_id' => null,
            'row_id' => null,
            'var_id' => null,
            'element_id' => null,
            'content_type' => null,
            'sort_order' => '0',
            'is_draft' => '0',
        ]);


    }

}
