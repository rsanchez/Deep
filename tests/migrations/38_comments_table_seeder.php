<?php

use Phinx\Migration\AbstractMigration;

class CommentsTableSeeder extends AbstractMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = $this->adapter->getConnection()->prepare('INSERT INTO comments (`comment_id`, `site_id`, `entry_id`, `channel_id`, `author_id`, `status`, `name`, `email`, `url`, `location`, `ip_address`, `comment_date`, `edit_date`, `comment`) VALUES (:comment_id, :site_id, :entry_id, :channel_id, :author_id, :status, :name, :email, :url, :location, :ip_address, :comment_date, :edit_date, :comment)');

        $query->execute([
            'comment_id' => '1',
            'site_id' => '1',
            'entry_id' => '7',
            'channel_id' => '1',
            'author_id' => '1',
            'status' => 'o',
            'name' => 'Admin',
            'email' => 'deep@deep.dev',
            'url' => '',
            'location' => '',
            'ip_address' => '127.0.0.1',
            'comment_date' => '1402777896',
            'edit_date' => null,
            'comment' => 'Here\'s my comment',
        ]);


    }

}
