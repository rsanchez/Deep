<?php

use Phinx\Migration\AbstractMigration;

class CreateAssetsSourcesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('assets_sources');

        $table->addColumn('source_id', 'integer');
        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('name', 'string', ['limit' => 255]);
        $table->addColumn('settings', 'text', ['limit' => 65535]);
    
        $table->create();
    }
}
