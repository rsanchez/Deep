<?php

use Phinx\Migration\AbstractMigration;

class CreateFilesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('files', ['id' => 'file_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('title', 'string', ['null' => true]);
        $table->addColumn('upload_location_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('rel_path', 'string', ['null' => true]);
        $table->addColumn('mime_type', 'string', ['null' => true]);
        $table->addColumn('file_name', 'string', ['null' => true]);
        $table->addColumn('file_size', 'integer', ['null' => true]);
        $table->addColumn('description', 'text', ['null' => true]);
        $table->addColumn('credit', 'string', ['null' => true]);
        $table->addColumn('location', 'string', ['null' => true]);
        $table->addColumn('uploaded_by_member_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('upload_date', 'integer', ['null' => true]);
        $table->addColumn('modified_by_member_id', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('modified_date', 'integer', ['null' => true]);
        $table->addColumn('file_hw_original', 'string', ['limit' => 20]);
    
        $table->create();
    }
}
