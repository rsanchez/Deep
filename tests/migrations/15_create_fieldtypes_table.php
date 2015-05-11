<?php

use Phinx\Migration\AbstractMigration;

class CreateFieldtypesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('fieldtypes', ['id' => 'fieldtype_id']);

        $table->addColumn('name', 'string', ['limit' => 50]);
        $table->addColumn('version', 'string', ['limit' => 12]);
        $table->addColumn('settings', 'text', ['null' => true]);
        $table->addColumn('has_global_settings', 'string', ['limit' => 1, 'null' => true]);
    
        $table->create();
    }
}
