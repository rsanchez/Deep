<?php

use Phinx\Migration\AbstractMigration;

class MembersTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO members (`member_id`, `group_id`, `username`, `screen_name`, `password`, `salt`, `unique_id`, `crypt_key`, `authcode`, `email`, `url`, `location`, `occupation`, `interests`, `bday_d`, `bday_m`, `bday_y`, `aol_im`, `yahoo_im`, `msn_im`, `icq`, `bio`, `signature`, `avatar_filename`, `avatar_width`, `avatar_height`, `photo_filename`, `photo_width`, `photo_height`, `sig_img_filename`, `sig_img_width`, `sig_img_height`, `ignore_list`, `private_messages`, `accept_messages`, `last_view_bulletins`, `last_bulletin_date`, `ip_address`, `join_date`, `last_visit`, `last_activity`, `total_entries`, `total_comments`, `total_forum_topics`, `total_forum_posts`, `last_entry_date`, `last_comment_date`, `last_forum_post_date`, `last_email_date`, `in_authorlist`, `accept_admin_email`, `accept_user_email`, `notify_by_default`, `notify_of_pm`, `display_avatars`, `display_signatures`, `parse_smileys`, `smart_notifications`, `language`, `timezone`, `time_format`, `date_format`, `include_seconds`, `cp_theme`, `profile_theme`, `forum_theme`, `tracker`, `template_size`, `notepad`, `notepad_size`, `quick_links`, `quick_tabs`, `show_sidebar`, `pmember_id`, `rte_enabled`, `rte_toolset_id`) VALUES (:member_id, :group_id, :username, :screen_name, :password, :salt, :unique_id, :crypt_key, :authcode, :email, :url, :location, :occupation, :interests, :bday_d, :bday_m, :bday_y, :aol_im, :yahoo_im, :msn_im, :icq, :bio, :signature, :avatar_filename, :avatar_width, :avatar_height, :photo_filename, :photo_width, :photo_height, :sig_img_filename, :sig_img_width, :sig_img_height, :ignore_list, :private_messages, :accept_messages, :last_view_bulletins, :last_bulletin_date, :ip_address, :join_date, :last_visit, :last_activity, :total_entries, :total_comments, :total_forum_topics, :total_forum_posts, :last_entry_date, :last_comment_date, :last_forum_post_date, :last_email_date, :in_authorlist, :accept_admin_email, :accept_user_email, :notify_by_default, :notify_of_pm, :display_avatars, :display_signatures, :parse_smileys, :smart_notifications, :language, :timezone, :time_format, :date_format, :include_seconds, :cp_theme, :profile_theme, :forum_theme, :tracker, :template_size, :notepad, :notepad_size, :quick_links, :quick_tabs, :show_sidebar, :pmember_id, :rte_enabled, :rte_toolset_id)');

        $query->execute([
            'member_id' => 1,
            'group_id' => 1,
            'username' => 'admin',
            'screen_name' => 'Admin',
            'password' => 'f8953febae05261b126f50d94be4fe7d5b21e4be4917d892acf09bcb0895530ada9cfda9123fc8e0d30e6c2c9de86df4e6e3026e996ffa32256a047e734b865f',
        'salt' => 'qT="2]Tku!5%$WV6f|v\'GNQ}M~d[WS48s)13vZFo\'KHZx=OuGl}939gLN*%Qg_d>q-@V9/wP+BQ<+F2mG85l0tcESv8+cx))$]l(HUv{+weqlt6I(6MmhJ#[fp,?KzH`',
            'unique_id' => 'c9f50b3ff72f3087bf2d69c8d2f5a9e1ceb7f631',
            'crypt_key' => 'd982aa11bb69efd07e9eedbd6b79ccb1037428eb',
            'authcode' => null,
            'email' => 'deep@deep.dev',
            'url' => '',
            'location' => '',
            'occupation' => '',
            'interests' => '',
            'bday_d' => null,
            'bday_m' => null,
            'bday_y' => null,
            'aol_im' => '',
            'yahoo_im' => '',
            'msn_im' => '',
            'icq' => '',
            'bio' => '',
            'signature' => null,
            'avatar_filename' => null,
            'avatar_width' => null,
            'avatar_height' => null,
            'photo_filename' => null,
            'photo_width' => null,
            'photo_height' => null,
            'sig_img_filename' => null,
            'sig_img_width' => null,
            'sig_img_height' => null,
            'ignore_list' => null,
            'private_messages' => 0,
            'accept_messages' => 'y',
            'last_view_bulletins' => 0,
            'last_bulletin_date' => 0,
            'ip_address' => '127.0.0.1',
            'join_date' => 1399513396,
            'last_visit' => 1401135154,
            'last_activity' => 1401161075,
            'total_entries' => 11,
            'total_comments' => 0,
            'total_forum_topics' => 0,
            'total_forum_posts' => 0,
            'last_entry_date' => 1399602903,
            'last_comment_date' => 0,
            'last_forum_post_date' => 0,
            'last_email_date' => 0,
            'in_authorlist' => 'n',
            'accept_admin_email' => 'y',
            'accept_user_email' => 'y',
            'notify_by_default' => 'y',
            'notify_of_pm' => 'y',
            'display_avatars' => 'y',
            'display_signatures' => 'y',
            'parse_smileys' => 'y',
            'smart_notifications' => 'y',
            'language' => 'english',
            'timezone' => 'America/Chicago',
            'time_format' => '12',
            'date_format' => '%n/%j/%y',
            'include_seconds' => 'n',
            'cp_theme' => null,
            'profile_theme' => null,
            'forum_theme' => null,
            'tracker' => null,
            'template_size' => '28',
            'notepad' => null,
            'notepad_size' => '18',
            'quick_links' => '',
            'quick_tabs' => null,
            'show_sidebar' => 'n',
            'pmember_id' => 0,
            'rte_enabled' => 'y',
            'rte_toolset_id' => 0,
        ]);


    }

}
