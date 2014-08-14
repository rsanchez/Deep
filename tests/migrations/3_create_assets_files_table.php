<?php

use Phinx\Migration\AbstractMigration;

class CreateAssetsFilesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('assets_files');

        $table->addColumn('file_id', 'integer');
        $table->addColumn('folder_id', 'integer');
        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('source_id', 'integer', ['null' => true]);
        $table->addColumn('filedir_id', 'integer', ['null' => true]);
        $table->addColumn('file_name', 'string', ['limit' => 255]);
        $table->addColumn('title', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('date', 'integer', ['null' => true]);
        $table->addColumn('alt_text', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('caption', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('author', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('desc', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('location', 'text', ['limit' => 255, 'null' => true]);
        $table->addColumn('keywords', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('date_modified', 'integer', ['null' => true]);
        $table->addColumn('kind', 'string', ['limit' => 5, 'null' => true]);
        $table->addColumn('width', 'integer', ['null' => true]);
        $table->addColumn('height', 'integer', ['null' => true]);
        $table->addColumn('size', 'integer', ['null' => true]);
        $table->addColumn('search_keywords', 'text', ['limit' => 65535, 'null' => true]);
    
        $table->create();
    }
}
