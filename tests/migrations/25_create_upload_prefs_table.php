<?php

use Phinx\Migration\AbstractMigration;

class CreateUploadPrefsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('upload_prefs');

        $table->addColumn('site_id', 'integer');
        $table->addColumn('name', 'string', ['limit' => 50]);
        $table->addColumn('server_path', 'string', ['limit' => 255]);
        $table->addColumn('url', 'string', ['limit' => 100]);
        $table->addColumn('allowed_types', 'string', ['limit' => 3]);
        $table->addColumn('max_size', 'string', ['limit' => 16, 'null' => true]);
        $table->addColumn('max_height', 'string', ['limit' => 6, 'null' => true]);
        $table->addColumn('max_width', 'string', ['limit' => 6, 'null' => true]);
        $table->addColumn('properties', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('pre_format', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('post_format', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('file_properties', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('file_pre_format', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('file_post_format', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('cat_group', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('batch_location', 'string', ['limit' => 255, 'null' => true]);
    
        $table->create();
    }
}
