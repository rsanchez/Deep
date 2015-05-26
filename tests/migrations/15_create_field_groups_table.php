<?php

use Phinx\Migration\AbstractMigration;

class CreateFieldGroupsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('field_groups', ['id' => 'group_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_name', 'string', ['limit' => 50]);
    
        $table->create();
    }
}
