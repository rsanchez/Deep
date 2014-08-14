<?php

use Phinx\Migration\AbstractMigration;

class CreateSitesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('sites');

        $table->addColumn('site_id', 'integer');
        $table->addColumn('site_label', 'string', ['limit' => 100]);
        $table->addColumn('site_name', 'string', ['limit' => 50]);
        $table->addColumn('site_description', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('site_system_preferences', 'text', ['limit' => 16777215]);
        $table->addColumn('site_mailinglist_preferences', 'text', ['limit' => 65535]);
        $table->addColumn('site_member_preferences', 'text', ['limit' => 65535]);
        $table->addColumn('site_template_preferences', 'text', ['limit' => 65535]);
        $table->addColumn('site_channel_preferences', 'text', ['limit' => 65535]);
        $table->addColumn('site_bootstrap_checksums', 'text', ['limit' => 65535]);
        $table->addColumn('site_pages', 'text', ['limit' => 65535]);
    
        $table->create();
    }
}
