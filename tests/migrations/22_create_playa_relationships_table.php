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
        $table = $this->table('playa_relationships');

        $table->addColumn('rel_id', 'integer');
        $table->addColumn('parent_entry_id', 'integer', ['null' => true]);
        $table->addColumn('parent_field_id', 'integer', ['null' => true]);
        $table->addColumn('parent_col_id', 'integer', ['null' => true]);
        $table->addColumn('parent_row_id', 'integer', ['null' => true]);
        $table->addColumn('parent_var_id', 'integer', ['null' => true]);
        $table->addColumn('parent_is_draft', 'integer', ['null' => true]);
        $table->addColumn('child_entry_id', 'integer', ['null' => true]);
        $table->addColumn('rel_order', 'integer', ['null' => true]);
    
        $table->create();
    }
}
