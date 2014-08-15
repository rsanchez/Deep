<?php

use Phinx\Migration\AbstractMigration;

class ChannelGridField12TableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO channel_grid_field_12 (`row_id`, `entry_id`, `row_order`, `col_id_1`, `col_id_2`, `col_id_3`) VALUES (:row_id, :entry_id, :row_order, :col_id_1, :col_id_2, :col_id_3)');

        $query->execute([
            'row_id' => '1',
            'entry_id' => '7',
            'row_order' => '0',
            'col_id_1' => 'Text',
            'col_id_2' => '{filedir_1}22bb4d5d6211e00f.jpg',
            'col_id_3' => '',
        ]);

        $query->execute([
            'row_id' => '2',
            'entry_id' => '8',
            'row_order' => '0',
            'col_id_1' => 'Text',
            'col_id_2' => '{filedir_1}b87ba69c47a83184.jpg',
            'col_id_3' => '',
        ]);

        $query->execute([
            'row_id' => '3',
            'entry_id' => '8',
            'row_order' => '1',
            'col_id_1' => 'Text 2',
            'col_id_2' => '{filedir_1}22bb4d5d6211e00f.jpg',
            'col_id_3' => '',
        ]);


    }

}
