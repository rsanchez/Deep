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
        $table = $this->table('category_field_data', ['id' => 'cat_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_id', 'integer', ['signed' => false]);
        $table->addColumn('field_id_1', 'text', ['null' => true]);
        $table->addColumn('field_ft_1', 'string', ['limit' => 40, 'null' => true]);
    
        $table->create();
    }
}
