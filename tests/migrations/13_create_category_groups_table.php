<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryGroupsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('category_groups', ['id' => 'group_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_name', 'string', ['limit' => 50]);
        $table->addColumn('sort_order', 'string', ['limit' => 1]);
        $table->addColumn('exclude_group', 'boolean', ['signed' => false]);
        $table->addColumn('field_html_formatting', 'string', ['limit' => 4]);
        $table->addColumn('can_edit_categories', 'text', ['null' => true]);
        $table->addColumn('can_delete_categories', 'text', ['null' => true]);
    
        $table->create();
    }
}
