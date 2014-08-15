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
        $table = $this->table('categories');

        $table->addColumn('cat_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->addColumn('parent_id', 'integer');
        $table->addColumn('cat_name', 'string', ['limit' => 100]);
        $table->addColumn('cat_url_title', 'string', ['limit' => 75]);
        $table->addColumn('cat_description', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('cat_image', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('cat_order', 'integer');
    
        $table->create();
    }
}
