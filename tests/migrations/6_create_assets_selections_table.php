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
        $table = $this->table('assets_selections', ['id' => false]);

        $table->addColumn('file_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('entry_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('field_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('col_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('row_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('var_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('element_id', 'string', ['null' => true]);
        $table->addColumn('content_type', 'string', ['null' => true]);
        $table->addColumn('sort_order', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('is_draft', 'boolean', ['signed' => false, 'null' => true]);
    
        $table->create();
    }
}
