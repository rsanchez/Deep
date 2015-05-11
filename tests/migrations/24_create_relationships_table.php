<?php

use Phinx\Migration\AbstractMigration;

class CreateRelationshipsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('relationships', ['id' => 'relationship_id']);

        $table->addColumn('parent_id', 'integer', ['signed' => false]);
        $table->addColumn('child_id', 'integer', ['signed' => false]);
        $table->addColumn('field_id', 'integer', ['signed' => false]);
        $table->addColumn('grid_field_id', 'integer', ['signed' => false]);
        $table->addColumn('grid_col_id', 'integer', ['signed' => false]);
        $table->addColumn('grid_row_id', 'integer', ['signed' => false]);
        $table->addColumn('order', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
