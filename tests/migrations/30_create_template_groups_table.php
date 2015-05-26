<?php

use Phinx\Migration\AbstractMigration;

class CreateTemplateGroupsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('template_groups', ['id' => 'group_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_name', 'string', ['limit' => 50]);
        $table->addColumn('group_order', 'integer', ['signed' => false]);
        $table->addColumn('is_site_default', 'string', ['limit' => 1]);
    
        $table->create();
    }
}
