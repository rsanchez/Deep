<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoriesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('categories', ['id' => 'cat_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_id', 'integer', ['signed' => false]);
        $table->addColumn('parent_id', 'integer', ['signed' => false]);
        $table->addColumn('cat_name', 'string', ['limit' => 100]);
        $table->addColumn('cat_url_title', 'string', ['limit' => 75]);
        $table->addColumn('cat_description', 'text', ['null' => true]);
        $table->addColumn('cat_image', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('cat_order', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
