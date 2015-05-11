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
        $table = $this->table('assets_folders', ['id' => 'folder_id']);

        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('folder_name', 'string');
        $table->addColumn('full_path', 'string', ['null' => true]);
        $table->addColumn('parent_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('source_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('filedir_id', 'integer', ['signed' => false, 'null' => true]);
    
        $table->create();
    }
}
