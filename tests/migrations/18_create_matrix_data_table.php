<?php

use Phinx\Migration\AbstractMigration;

class CreateMatrixDataTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('matrix_data');

        $table->addColumn('row_id', 'integer');
        $table->addColumn('site_id', 'integer', ['null' => true]);
        $table->addColumn('entry_id', 'integer', ['null' => true]);
        $table->addColumn('field_id', 'integer', ['null' => true]);
        $table->addColumn('var_id', 'integer', ['null' => true]);
        $table->addColumn('is_draft', 'boolean', ['null' => true]);
        $table->addColumn('row_order', 'integer', ['null' => true]);
        $table->addColumn('col_id_1', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('col_id_2', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('col_id_3', 'text', ['limit' => 65535, 'null' => true]);
    
        $table->create();
    }
}
