<?php

use Phinx\Migration\AbstractMigration;

class ChannelsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO channels (`channel_id`, `site_id`, `channel_name`, `channel_title`, `channel_url`, `channel_description`, `channel_lang`, `total_entries`, `total_comments`, `last_entry_date`, `last_comment_date`, `cat_group`, `status_group`, `deft_status`, `field_group`, `search_excerpt`, `deft_category`, `deft_comments`, `channel_require_membership`, `channel_max_chars`, `channel_html_formatting`, `channel_allow_img_urls`, `channel_auto_link_urls`, `channel_notify`, `channel_notify_emails`, `comment_url`, `comment_system_enabled`, `comment_require_membership`, `comment_use_captcha`, `comment_moderate`, `comment_max_chars`, `comment_timelock`, `comment_require_email`, `comment_text_formatting`, `comment_html_formatting`, `comment_allow_img_urls`, `comment_auto_link_urls`, `comment_notify`, `comment_notify_authors`, `comment_notify_emails`, `comment_expiration`, `search_results_url`, `show_button_cluster`, `rss_url`, `enable_versioning`, `max_revisions`, `default_entry_title`, `url_title_prefix`, `live_look_template`) VALUES (:channel_id, :site_id, :channel_name, :channel_title, :channel_url, :channel_description, :channel_lang, :total_entries, :total_comments, :last_entry_date, :last_comment_date, :cat_group, :status_group, :deft_status, :field_group, :search_excerpt, :deft_category, :deft_comments, :channel_require_membership, :channel_max_chars, :channel_html_formatting, :channel_allow_img_urls, :channel_auto_link_urls, :channel_notify, :channel_notify_emails, :comment_url, :comment_system_enabled, :comment_require_membership, :comment_use_captcha, :comment_moderate, :comment_max_chars, :comment_timelock, :comment_require_email, :comment_text_formatting, :comment_html_formatting, :comment_allow_img_urls, :comment_auto_link_urls, :comment_notify, :comment_notify_authors, :comment_notify_emails, :comment_expiration, :search_results_url, :show_button_cluster, :rss_url, :enable_versioning, :max_revisions, :default_entry_title, :url_title_prefix, :live_look_template)');

        $query->execute([
            'channel_id' => '1',
            'site_id' => '1',
            'channel_name' => 'entries',
            'channel_title' => 'Entries',
            'channel_url' => '/entries',
            'channel_description' => '',
            'channel_lang' => 'en',
            'total_entries' => '2',
            'total_comments' => '1',
            'last_entry_date' => '1399602660',
            'last_comment_date' => '1402777896',
            'cat_group' => '1|2',
            'status_group' => '1',
            'deft_status' => 'open',
            'field_group' => '1',
            'search_excerpt' => '1',
            'deft_category' => '',
            'deft_comments' => 'y',
            'channel_require_membership' => 'y',
            'channel_max_chars' => null,
            'channel_html_formatting' => 'all',
            'channel_allow_img_urls' => 'y',
            'channel_auto_link_urls' => 'n',
            'channel_notify' => 'n',
            'channel_notify_emails' => '',
            'comment_url' => '',
            'comment_system_enabled' => 'y',
            'comment_require_membership' => 'n',
            'comment_use_captcha' => 'n',
            'comment_moderate' => 'n',
            'comment_max_chars' => '5000',
            'comment_timelock' => '0',
            'comment_require_email' => 'y',
            'comment_text_formatting' => 'xhtml',
            'comment_html_formatting' => 'safe',
            'comment_allow_img_urls' => 'n',
            'comment_auto_link_urls' => 'y',
            'comment_notify' => 'n',
            'comment_notify_authors' => 'n',
            'comment_notify_emails' => '',
            'comment_expiration' => '0',
            'search_results_url' => '',
            'show_button_cluster' => 'y',
            'rss_url' => '',
            'enable_versioning' => 'n',
            'max_revisions' => '10',
            'default_entry_title' => '',
            'url_title_prefix' => '',
            'live_look_template' => '0',
        ]);

        $query->execute([
            'channel_id' => '2',
            'site_id' => '1',
            'channel_name' => 'related',
            'channel_title' => 'Related',
            'channel_url' => '/related',
            'channel_description' => '',
            'channel_lang' => 'en',
            'total_entries' => '6',
            'total_comments' => '0',
            'last_entry_date' => '1399602120',
            'last_comment_date' => '0',
            'cat_group' => '',
            'status_group' => '1',
            'deft_status' => 'open',
            'field_group' => null,
            'search_excerpt' => null,
            'deft_category' => '',
            'deft_comments' => 'y',
            'channel_require_membership' => 'y',
            'channel_max_chars' => null,
            'channel_html_formatting' => 'all',
            'channel_allow_img_urls' => 'y',
            'channel_auto_link_urls' => 'n',
            'channel_notify' => 'n',
            'channel_notify_emails' => '',
            'comment_url' => '',
            'comment_system_enabled' => 'y',
            'comment_require_membership' => 'n',
            'comment_use_captcha' => 'n',
            'comment_moderate' => 'n',
            'comment_max_chars' => '5000',
            'comment_timelock' => '0',
            'comment_require_email' => 'y',
            'comment_text_formatting' => 'xhtml',
            'comment_html_formatting' => 'safe',
            'comment_allow_img_urls' => 'n',
            'comment_auto_link_urls' => 'y',
            'comment_notify' => 'n',
            'comment_notify_authors' => 'n',
            'comment_notify_emails' => '',
            'comment_expiration' => '0',
            'search_results_url' => '',
            'show_button_cluster' => 'y',
            'rss_url' => '',
            'enable_versioning' => 'n',
            'max_revisions' => '10',
            'default_entry_title' => '',
            'url_title_prefix' => '',
            'live_look_template' => '0',
        ]);


    }

}
