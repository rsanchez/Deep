<?php

use Phinx\Migration\AbstractMigration;

class CreateTemplatesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('templates', ['id' => 'template_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('group_id', 'integer', ['signed' => false]);
        $table->addColumn('template_name', 'string', ['limit' => 50]);
        $table->addColumn('save_template_file', 'string', ['limit' => 1]);
        $table->addColumn('template_type', 'string', ['limit' => 16]);
        $table->addColumn('template_data', 'text', ['null' => true]);
        $table->addColumn('template_notes', 'text', ['null' => true]);
        $table->addColumn('edit_date', 'integer');
        $table->addColumn('last_author_id', 'integer', ['signed' => false]);
        $table->addColumn('cache', 'string', ['limit' => 1]);
        $table->addColumn('refresh', 'integer', ['signed' => false]);
        $table->addColumn('no_auth_bounce', 'string', ['limit' => 50]);
        $table->addColumn('enable_http_auth', 'string', ['limit' => 1]);
        $table->addColumn('allow_php', 'string', ['limit' => 1]);
        $table->addColumn('php_parse_location', 'string', ['limit' => 1]);
        $table->addColumn('hits', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
