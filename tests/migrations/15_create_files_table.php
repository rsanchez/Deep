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
        $table = $this->table('files');

        $table->addColumn('file_id', 'integer');
        $table->addColumn('site_id', 'integer', ['null' => true]);
        $table->addColumn('title', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('upload_location_id', 'integer', ['null' => true]);
        $table->addColumn('rel_path', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('mime_type', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('file_name', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('file_size', 'integer', ['null' => true]);
        $table->addColumn('description', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('credit', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('location', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('uploaded_by_member_id', 'integer', ['null' => true]);
        $table->addColumn('upload_date', 'integer', ['null' => true]);
        $table->addColumn('modified_by_member_id', 'integer', ['null' => true]);
        $table->addColumn('modified_date', 'integer', ['null' => true]);
        $table->addColumn('file_hw_original', 'string', ['limit' => 20]);
    
        $table->create();
    }
}
