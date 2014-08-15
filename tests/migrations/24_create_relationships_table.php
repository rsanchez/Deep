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
        $table = $this->table('relationships');

        $table->addColumn('relationship_id', 'integer');
        $table->addColumn('parent_id', 'integer');
        $table->addColumn('child_id', 'integer');
        $table->addColumn('field_id', 'integer');
        $table->addColumn('grid_field_id', 'integer');
        $table->addColumn('grid_col_id', 'integer');
        $table->addColumn('grid_row_id', 'integer');
        $table->addColumn('order', 'integer');
    
        $table->create();
    }
}
