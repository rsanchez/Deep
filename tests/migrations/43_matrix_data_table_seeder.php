<?php

use Phinx\Migration\AbstractMigration;

class MatrixDataTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO matrix_data (`row_id`, `site_id`, `entry_id`, `field_id`, `var_id`, `is_draft`, `row_order`, `col_id_1`, `col_id_2`, `col_id_3`) VALUES (:row_id, :site_id, :entry_id, :field_id, :var_id, :is_draft, :row_order, :col_id_1, :col_id_2, :col_id_3)');

        $query->execute([
            'row_id' => '1',
            'site_id' => '1',
            'entry_id' => '7',
            'field_id' => '13',
            'var_id' => null,
            'is_draft' => '0',
            'row_order' => '1',
            'col_id_1' => 'Text',
            'col_id_2' => '{filedir_1}fbc59e86a565f8a3.jpg',
            'col_id_3' => '[2] [related-2] Related 2
[4] [related-4] Related 4',
        ]);

        $query->execute([
            'row_id' => '2',
            'site_id' => '1',
            'entry_id' => '8',
            'field_id' => '13',
            'var_id' => null,
            'is_draft' => '0',
            'row_order' => '1',
            'col_id_1' => 'Text',
            'col_id_2' => '{filedir_1}fbc59e86a565f8a3.jpg',
            'col_id_3' => '[6] [related-6] Related 6',
        ]);

        $query->execute([
            'row_id' => '3',
            'site_id' => '1',
            'entry_id' => '8',
            'field_id' => '13',
            'var_id' => null,
            'is_draft' => '0',
            'row_order' => '2',
            'col_id_1' => 'Text 2',
            'col_id_2' => '{filedir_1}c07cd109414fa275.jpg',
            'col_id_3' => '[4] [related-4] Related 4',
        ]);


    }

}
