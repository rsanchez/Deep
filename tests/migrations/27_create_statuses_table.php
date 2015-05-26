<?php

use Phinx\Migration\AbstractMigration;

class CreateStatusesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('statuses', ['id' => 'status_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_id', 'integer', ['signed' => false]);
        $table->addColumn('status', 'string', ['limit' => 50]);
        $table->addColumn('status_order', 'integer', ['signed' => false]);
        $table->addColumn('highlight', 'string', ['limit' => 30]);
    
        $table->create();
    }
}
