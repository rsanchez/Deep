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
        $table = $this->table('matrix_data', ['id' => 'row_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('entry_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('field_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('var_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('is_draft', 'boolean', ['signed' => false, 'null' => true]);
        $table->addColumn('row_order', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('col_id_1', 'text', ['null' => true]);
        $table->addColumn('col_id_2', 'text', ['null' => true]);
        $table->addColumn('col_id_3', 'text', ['null' => true]);
    
        $table->create();
    }
}
