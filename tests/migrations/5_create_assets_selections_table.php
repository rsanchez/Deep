<?php

use Phinx\Migration\AbstractMigration;

class CreateAssetsSelectionsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('assets_selections');

        $table->addColumn('file_id', 'integer', ['null' => true]);
        $table->addColumn('entry_id', 'integer', ['null' => true]);
        $table->addColumn('field_id', 'integer', ['null' => true]);
        $table->addColumn('col_id', 'integer', ['null' => true]);
        $table->addColumn('row_id', 'integer', ['null' => true]);
        $table->addColumn('var_id', 'integer', ['null' => true]);
        $table->addColumn('element_id', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('content_type', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('sort_order', 'integer', ['null' => true]);
        $table->addColumn('is_draft', 'boolean', ['null' => true]);
    
        $table->create();
    }
}
