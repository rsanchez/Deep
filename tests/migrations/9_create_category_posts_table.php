<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryPostsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('category_posts');

        $table->addColumn('entry_id', 'integer');
        $table->addColumn('cat_id', 'integer');
    
        $table->create();
    }
}
