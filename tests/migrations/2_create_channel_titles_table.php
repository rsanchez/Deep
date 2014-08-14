<?php

use Phinx\Migration\AbstractMigration;

class CreateChannelTitlesTable extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('channel_titles');

        $table->addColumn('entry_id', 'integer');
        $table->addColumn('site_id', 'integer');
        $table->addColumn('channel_id', 'integer');
        $table->addColumn('author_id', 'integer');
        $table->addColumn('forum_topic_id', 'integer', ['null' => true]);
        $table->addColumn('ip_address', 'string', ['limit' => 45]);
        $table->addColumn('title', 'string', ['limit' => 100]);
        $table->addColumn('url_title', 'string', ['limit' => 75]);
        $table->addColumn('status', 'string', ['limit' => 50]);
        $table->addColumn('versioning_enabled', 'string', ['limit' => 1]);
        $table->addColumn('view_count_one', 'integer');
        $table->addColumn('view_count_two', 'integer');
        $table->addColumn('view_count_three', 'integer');
        $table->addColumn('view_count_four', 'integer');
        $table->addColumn('allow_comments', 'string', ['limit' => 1]);
        $table->addColumn('sticky', 'string', ['limit' => 1]);
        $table->addColumn('entry_date', 'integer');
        $table->addColumn('year', 'string', ['limit' => 4]);
        $table->addColumn('month', 'string', ['limit' => 2]);
        $table->addColumn('day', 'string', ['limit' => 3]);
        $table->addColumn('expiration_date', 'integer');
        $table->addColumn('comment_expiration_date', 'integer');
        $table->addColumn('edit_date', 'integer', ['null' => true]);
        $table->addColumn('recent_comment_date', 'integer', ['null' => true]);
        $table->addColumn('comment_total', 'integer');
    
        $table->create();
    }
}
