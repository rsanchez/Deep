<?php

use Phinx\Migration\AbstractMigration;

class CreateAssetsFoldersTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('assets_folders');

        $table->addColumn('folder_id', 'integer');
        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('folder_name', 'string', ['limit' => 255]);
        $table->addColumn('full_path', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('parent_id', 'integer', ['null' => true]);
        $table->addColumn('source_id', 'integer', ['null' => true]);
        $table->addColumn('filedir_id', 'integer', ['null' => true]);
    
        $table->create();
    }
}
