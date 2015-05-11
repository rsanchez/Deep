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
        $table = $this->table('assets_files', ['id' => 'file_id']);

        $table->addColumn('folder_id', 'integer', ['signed' => false]);
        $table->addColumn('source_type', 'string', ['limit' => 2]);
        $table->addColumn('source_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('filedir_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('file_name', 'string');
        $table->addColumn('title', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('date', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('alt_text', 'text', ['null' => true]);
        $table->addColumn('caption', 'text', ['null' => true]);
        $table->addColumn('author', 'text', ['null' => true]);
        $table->addColumn('desc', 'text', ['null' => true]);
        $table->addColumn('location', 'text', ['null' => true]);
        $table->addColumn('keywords', 'text', ['null' => true]);
        $table->addColumn('date_modified', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('kind', 'string', ['limit' => 5, 'null' => true]);
        $table->addColumn('width', 'integer', ['null' => true]);
        $table->addColumn('height', 'integer', ['null' => true]);
        $table->addColumn('size', 'integer', ['null' => true]);
        $table->addColumn('search_keywords', 'text', ['null' => true]);
    
        $table->create();
    }
}
