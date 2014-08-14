<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelGridField12Table extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channel_grid_field_12');

        $table->addColumn('row_id', 'integer');
        $table->addColumn('entry_id', 'integer', ['null' => true]);
        $table->addColumn('row_order', 'integer', ['null' => true]);
        $table->addColumn('col_id_1', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('col_id_2', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('col_id_3', 'string', ['limit' => 8, 'null' => true]);
    
        $table->create();
    }
}
