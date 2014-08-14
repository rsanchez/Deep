<?php

use Phinx\Migration\AbstractMigration;

class CreateMembersTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('members');

        $table->addColumn('member_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->addColumn('username', 'string', ['limit' => 50]);
        $table->addColumn('screen_name', 'string', ['limit' => 50]);
        $table->addColumn('password', 'string', ['limit' => 128]);
        $table->addColumn('salt', 'string', ['limit' => 128]);
        $table->addColumn('unique_id', 'string', ['limit' => 40]);
        $table->addColumn('crypt_key', 'string', ['limit' => 40, 'null' => true]);
        $table->addColumn('authcode', 'string', ['limit' => 10, 'null' => true]);
        $table->addColumn('email', 'string', ['limit' => 75]);
        $table->addColumn('url', 'string', ['limit' => 150, 'null' => true]);
        $table->addColumn('location', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('occupation', 'string', ['limit' => 80, 'null' => true]);
        $table->addColumn('interests', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('bday_d', 'integer', ['null' => true]);
        $table->addColumn('bday_m', 'integer', ['null' => true]);
        $table->addColumn('bday_y', 'integer', ['null' => true]);
        $table->addColumn('aol_im', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('yahoo_im', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('msn_im', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('icq', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('bio', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('signature', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('avatar_filename', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('avatar_width', 'integer', ['null' => true]);
        $table->addColumn('avatar_height', 'integer', ['null' => true]);
        $table->addColumn('photo_filename', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('photo_width', 'integer', ['null' => true]);
        $table->addColumn('photo_height', 'integer', ['null' => true]);
        $table->addColumn('sig_img_filename', 'string', ['limit' => 120, 'null' => true]);
        $table->addColumn('sig_img_width', 'integer', ['null' => true]);
        $table->addColumn('sig_img_height', 'integer', ['null' => true]);
        $table->addColumn('ignore_list', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('private_messages', 'integer');
        $table->addColumn('accept_messages', 'string', ['limit' => 1]);
        $table->addColumn('last_view_bulletins', 'integer');
        $table->addColumn('last_bulletin_date', 'integer');
        $table->addColumn('ip_address', 'string', ['limit' => 45]);
        $table->addColumn('join_date', 'integer');
        $table->addColumn('last_visit', 'integer');
        $table->addColumn('last_activity', 'integer');
        $table->addColumn('total_entries', 'integer');
        $table->addColumn('total_comments', 'integer');
        $table->addColumn('total_forum_topics', 'integer');
        $table->addColumn('total_forum_posts', 'integer');
        $table->addColumn('last_entry_date', 'integer');
        $table->addColumn('last_comment_date', 'integer');
        $table->addColumn('last_forum_post_date', 'integer');
        $table->addColumn('last_email_date', 'integer');
        $table->addColumn('in_authorlist', 'string', ['limit' => 1]);
        $table->addColumn('accept_admin_email', 'string', ['limit' => 1]);
        $table->addColumn('accept_user_email', 'string', ['limit' => 1]);
        $table->addColumn('notify_by_default', 'string', ['limit' => 1]);
        $table->addColumn('notify_of_pm', 'string', ['limit' => 1]);
        $table->addColumn('display_avatars', 'string', ['limit' => 1]);
        $table->addColumn('display_signatures', 'string', ['limit' => 1]);
        $table->addColumn('parse_smileys', 'string', ['limit' => 1]);
        $table->addColumn('smart_notifications', 'string', ['limit' => 1]);
        $table->addColumn('language', 'string', ['limit' => 50]);
        $table->addColumn('timezone', 'string', ['limit' => 50]);
        $table->addColumn('time_format', 'string', ['limit' => 2]);
        $table->addColumn('date_format', 'string', ['limit' => 8]);
        $table->addColumn('include_seconds', 'string', ['limit' => 1]);
        $table->addColumn('cp_theme', 'string', ['limit' => 32, 'null' => true]);
        $table->addColumn('profile_theme', 'string', ['limit' => 32, 'null' => true]);
        $table->addColumn('forum_theme', 'string', ['limit' => 32, 'null' => true]);
        $table->addColumn('tracker', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('template_size', 'string', ['limit' => 2]);
        $table->addColumn('notepad', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('notepad_size', 'string', ['limit' => 2]);
        $table->addColumn('quick_links', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('quick_tabs', 'text', ['limit' => 65535, 'null' => true]);
        $table->addColumn('show_sidebar', 'string', ['limit' => 1]);
        $table->addColumn('pmember_id', 'integer');
        $table->addColumn('rte_enabled', 'string', ['limit' => 1]);
        $table->addColumn('rte_toolset_id', 'integer');
    
        $table->create();
    }
}
