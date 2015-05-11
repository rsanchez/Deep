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
        $table = $this->table('assets_sources', ['id' => 'source_id']);

        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('name', 'string');
        $table->addColumn('settings', 'text');
    
        $table->create();
    }
}
