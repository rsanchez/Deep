<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryFieldDataTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('category_field_data');

        $table->addColumn('cat_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->addColumn('field_id_1', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('field_ft_1', 'string', ['limit' => 40, 'null' => true]);
    
        $table->create();
    }
}
