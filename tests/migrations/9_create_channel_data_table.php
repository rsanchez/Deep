<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelDataTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channel_data');

        $table->addColumn('entry_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('channel_id', 'integer');
        $table->addColumn('field_id_1', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_1', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_2', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_2', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_3', 'integer', ['null' => true]);
        $table->addColumn('field_dt_3', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('field_ft_3', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_4', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_4', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_5', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_5', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_6', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_6', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_7', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_7', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_8', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_8', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_9', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_9', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_10', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_10', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_11', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_11', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_12', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_12', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_13', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_13', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_14', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_14', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_15', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_15', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_16', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_16', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_17', 'string', ['limit' => 8, 'null' => true]);
        $table->addColumn('field_ft_17', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_18', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_18', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_19', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_19', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_20', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_20', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_21', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_21', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('field_id_22', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_22', 'text', ['limit' => 255, 'null' => true]);
    
        $table->create();
    }
}
