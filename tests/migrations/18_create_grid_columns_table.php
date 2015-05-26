<?php

use Phinx\Migration\AbstractMigration;

class CreateGridColumnsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('grid_columns', ['id' => 'col_id']);

        $table->addColumn('field_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('content_type', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('col_order', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('col_type', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('col_label', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('col_name', 'string', ['limit' => 32, 'null' => true]);
        $table->addColumn('col_instructions', 'text', ['null' => true]);
        $table->addColumn('col_required', 'string', ['limit' => 1, 'null' => true]);
        $table->addColumn('col_search', 'string', ['limit' => 1, 'null' => true]);
        $table->addColumn('col_width', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('col_settings', 'text', ['null' => true]);
    
        $table->create();
    }
}
