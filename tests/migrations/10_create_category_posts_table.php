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
        $table = $this->table('category_posts', ['id' => false]);

        $table->addColumn('entry_id', 'integer', ['signed' => false]);
        $table->addColumn('cat_id', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
