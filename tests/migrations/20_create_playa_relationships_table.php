<?php

use Phinx\Migration\AbstractMigration;

class CreatePlayaRelationshipsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('playa_relationships', ['id' => 'rel_id']);

        $table->addColumn('parent_entry_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('parent_field_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('parent_col_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('parent_row_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('parent_var_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('parent_is_draft', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('child_entry_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('rel_order', 'integer', ['signed' => false, 'null' => true]);
    
        $table->create();
    }
}
