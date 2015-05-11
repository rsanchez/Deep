<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelsTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channels', ['id' => 'channel_id']);

        $table->addColumn('site_id', 'integer', ['signed' => false]);
        $table->addColumn('channel_name', 'string', ['limit' => 40]);
        $table->addColumn('channel_title', 'string', ['limit' => 100]);
        $table->addColumn('channel_url', 'string', ['limit' => 100]);
        $table->addColumn('channel_description', 'string', ['null' => true]);
        $table->addColumn('channel_lang', 'string', ['limit' => 12]);
        $table->addColumn('total_entries', 'integer');
        $table->addColumn('total_comments', 'integer');
        $table->addColumn('last_entry_date', 'integer', ['signed' => false]);
        $table->addColumn('last_comment_date', 'integer', ['signed' => false]);
        $table->addColumn('cat_group', 'string', ['null' => true]);
        $table->addColumn('status_group', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('deft_status', 'string', ['limit' => 50]);
        $table->addColumn('field_group', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('search_excerpt', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('deft_category', 'string', ['limit' => 60, 'null' => true]);
        $table->addColumn('deft_comments', 'string', ['limit' => 1]);
        $table->addColumn('channel_require_membership', 'string', ['limit' => 1]);
        $table->addColumn('channel_max_chars', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('channel_html_formatting', 'string', ['limit' => 4]);
        $table->addColumn('channel_allow_img_urls', 'string', ['limit' => 1]);
        $table->addColumn('channel_auto_link_urls', 'string', ['limit' => 1]);
        $table->addColumn('channel_notify', 'string', ['limit' => 1]);
        $table->addColumn('channel_notify_emails', 'string', ['null' => true]);
        $table->addColumn('comment_url', 'string', ['limit' => 80, 'null' => true]);
        $table->addColumn('comment_system_enabled', 'string', ['limit' => 1]);
        $table->addColumn('comment_require_membership', 'string', ['limit' => 1]);
        $table->addColumn('comment_use_captcha', 'string', ['limit' => 1]);
        $table->addColumn('comment_moderate', 'string', ['limit' => 1]);
        $table->addColumn('comment_max_chars', 'integer', ['signed' => false, 'null' => true]);
        $table->addColumn('comment_timelock', 'integer', ['signed' => false]);
        $table->addColumn('comment_require_email', 'string', ['limit' => 1]);
        $table->addColumn('comment_text_formatting', 'string', ['limit' => 5]);
        $table->addColumn('comment_html_formatting', 'string', ['limit' => 4]);
        $table->addColumn('comment_allow_img_urls', 'string', ['limit' => 1]);
        $table->addColumn('comment_auto_link_urls', 'string', ['limit' => 1]);
        $table->addColumn('comment_notify', 'string', ['limit' => 1]);
        $table->addColumn('comment_notify_authors', 'string', ['limit' => 1]);
        $table->addColumn('comment_notify_emails', 'string', ['null' => true]);
        $table->addColumn('comment_expiration', 'integer', ['signed' => false]);
        $table->addColumn('search_results_url', 'string', ['limit' => 80, 'null' => true]);
        $table->addColumn('show_button_cluster', 'string', ['limit' => 1]);
        $table->addColumn('rss_url', 'string', ['limit' => 80, 'null' => true]);
        $table->addColumn('enable_versioning', 'string', ['limit' => 1]);
        $table->addColumn('max_revisions', 'integer', ['signed' => false]);
        $table->addColumn('default_entry_title', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('url_title_prefix', 'string', ['limit' => 80, 'null' => true]);
        $table->addColumn('live_look_template', 'integer', ['signed' => false]);
    
        $table->create();
    }
}
