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
        $table = $this->table('sites', ['id' => 'site_id']);

        $table->addColumn('site_label', 'string', ['limit' => 100]);
        $table->addColumn('site_name', 'string', ['limit' => 50]);
        $table->addColumn('site_description', 'text', ['null' => true]);
        $table->addColumn('site_system_preferences', 'text');
        $table->addColumn('site_mailinglist_preferences', 'text');
        $table->addColumn('site_member_preferences', 'text');
        $table->addColumn('site_template_preferences', 'text');
        $table->addColumn('site_channel_preferences', 'text');
        $table->addColumn('site_bootstrap_checksums', 'text');
        $table->addColumn('site_pages', 'text');
    
        $table->create();
    }
}
